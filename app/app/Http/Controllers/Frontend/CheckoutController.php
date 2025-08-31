<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\CheckoutRequest;
use App\Services\Frontend\CheckoutService;
use App\Services\Frontend\ClientService;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function __construct(
        private CheckoutService $checkoutService,
        private ClientService $clientService
    ) {}

    /**
     * Show checkout page
     */
    public function index(): View
    {
        $cartData = $this->checkoutService->getCartData();
        $countries = $this->clientService->getCountriesWithCities();
        
        return view('frontend.checkout.index', [
            'cartItems'  => $cartData['items'],
            'cartTotal'  => $cartData['total'],
            'countries'  => $countries,
        ]);
    }

    /**
     * Process checkout
     */
    public function process(CheckoutRequest $request): RedirectResponse
    {
        try {
            $order = $this->checkoutService->placeOrder($request->validated());
            return redirect()->route('orders.show', $order->id)
                ->with('success', 'Order placed successfully!');
        } catch (\Exception $e) {
            Log::error('Checkout process error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->withErrors(['checkout' => $e->getMessage()]);
        }
    }

    public function stripeCheckout(Request $request): JsonResponse
    {
        try {
            // Manual validation instead of CheckoutRequest to avoid redirect
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
                'address' => 'required|string|max:500',
                'country_id' => 'required|exists:countries,id',
                'city_id' => 'required|exists:cities,id',
                'notes' => 'nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Validation failed: ' . $validator->errors()->first()
                ], 422);
            }

            // Check if user is authenticated
            if (!auth()->check()) {
                return response()->json(['error' => 'Authentication required'], 401);
            }

            // Check Stripe configuration
            if (!config('stripe.key') || !config('stripe.secret')) {
                Log::error('Stripe configuration missing');
                return response()->json(['error' => 'Payment system not configured'], 500);
            }

            $session = $this->checkoutService->createStripeSession($validator->validated());
            
            return response()->json(['id' => $session->id]);
            
        } catch (\Exception $e) {
            Log::error('Stripe checkout error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'error' => 'Payment processing failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle Stripe webhooks
     */
    public function stripeWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('stripe.webhook_secret');

        try {
            if ($endpointSecret) {
                $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
            } else {
                $event = json_decode($payload, true);
            }

            // Handle the event
            switch ($event['type']) {
                case 'checkout.session.completed':
                    $this->handleSuccessfulPayment($event['data']['object']);
                    break;
                    
                case 'payment_intent.payment_failed':
                    $this->handleFailedPayment($event['data']['object']);
                    break;
                    
                default:
                    Log::info('Unhandled Stripe event type: ' . $event['type']);
            }

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('Stripe webhook error:', [
                'message' => $e->getMessage(),
                'payload' => $payload
            ]);
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Handle successful payment and create order
     */
    private function handleSuccessfulPayment($session)
    {
        try {
            DB::beginTransaction();

            $userId = $session['metadata']['user_id'];
            $user = \App\Models\User::find($userId);

            if (!$user) {
                throw new \Exception("User not found for payment session");
            }

            // Check if order already exists to prevent duplicates
            $existingPayment = Payment::where('stripe_session_id', $session['id'])->first();
            if ($existingPayment && $existingPayment->order) {
                Log::info('Order already exists for this session', ['session_id' => $session['id']]);
                DB::commit();
                return;
            }

            // Get cart items from session storage
            $cartItems = session("pending_order_items_{$userId}");
            
            if (!$cartItems) {
                // Fallback: try to get from current session if user is logged in
                if (auth()->id() == $userId) {
                    $cartData = $this->checkoutService->getCartData();
                    $cartItems = $cartData['items'];
                }
            }

            if (empty($cartItems)) {
                Log::warning('No cart items found for completed payment', [
                    'user_id' => $userId,
                    'session_id' => $session['id']
                ]);
                DB::rollBack();
                return;
            }

            // Create or update Payment record
            $payment = Payment::updateOrCreate(
                ['stripe_session_id' => $session['id']],
                [
                    'id' => $existingPayment->id ?? Str::uuid(),
                    'stripe_payment_intent_id' => $session['payment_intent'] ?? null,
                    'amount' => $session['amount_total'] / 100, // Convert from cents
                    'currency' => $session['currency'],
                    'status' => 'completed',
                    'payment_method' => 'stripe',
                ]
            );

            // Calculate total from cart items
            $calculatedTotal = collect($cartItems)->sum(function($item) {
                return floatval($item['price']) * intval($item['quantity']);
            });

            // Create Order
            $order = Order::create([
                'id' => Str::uuid(),
                'client_id' => $userId,
                'status' => 'confirmed',
                'total_amount' => $calculatedTotal,
                'payment_id' => $payment->id,
                'shipping_name' => $session['metadata']['user_name'] ?? '',
                'shipping_phone' => $session['metadata']['user_phone'] ?? '',
                'shipping_address' => $session['metadata']['user_address'] ?? '',
                'country_id' => $session['metadata']['country_id'] ?? null,
                'city_id' => $session['metadata']['city_id'] ?? null,
                'notes' => $session['metadata']['notes'] ?? null,
            ]);

            // Create Order Items and reduce product quantities
            foreach ($cartItems as $item) {
                $product = Product::find($item['id']);
                if (!$product) {
                    Log::warning('Product not found during order creation', ['product_id' => $item['id']]);
                    continue;
                }

                $requestedQuantity = intval($item['quantity']);
                
                // Check if enough quantity available
                if ($product->quantity < $requestedQuantity) {
                    Log::warning('Insufficient product quantity', [
                        'product_id' => $item['id'],
                        'requested' => $requestedQuantity,
                        'available' => $product->quantity
                    ]);
                    
                    // Use available quantity instead of requested
                    $requestedQuantity = $product->quantity;
                }

                if ($requestedQuantity > 0) {
                    // Create order item
                    OrderItem::create([
                        'id' => Str::uuid(),
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $requestedQuantity,
                        'price' => floatval($item['price']),
                        'total' => floatval($item['price']) * $requestedQuantity,
                    ]);

                    // Reduce product quantity atomically
                    $product->decrement('quantity', $requestedQuantity);
                    
                    Log::info('Product quantity reduced', [
                        'product_id' => $product->id,
                        'reduced_by' => $requestedQuantity,
                        'new_quantity' => $product->fresh()->quantity
                    ]);
                }
            }

            // Clear cart session and database
            $this->clearUserCartCompletely($userId);

            // Clear pending order data from session
            session()->forget([
                "pending_order_items_{$userId}",
                "pending_order_data_{$userId}"
            ]);

            DB::commit();

            Log::info('Order created successfully from Stripe webhook', [
                'order_id' => $order->id,
                'user_id' => $userId,
                'payment_id' => $payment->id,
                'total_amount' => $order->total_amount
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create order after payment:', [
                'message' => $e->getMessage(),
                'session_id' => $session['id'] ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            
            // You might want to send an email notification here for manual intervention
        }
    }

    /**
     * Handle failed payment
     */
    private function handleFailedPayment($paymentIntent)
    {
        Log::warning('Payment failed', [
            'payment_intent_id' => $paymentIntent['id'],
            'last_payment_error' => $paymentIntent['last_payment_error'] ?? null
        ]);

        // Update payment record if exists
        $payment = Payment::where('stripe_payment_intent_id', $paymentIntent['id'])->first();
        if ($payment) {
            $payment->update(['status' => 'failed']);
        }
    }

    /**
     * Completely clear user's cart from all sources
     */
    private function clearUserCartCompletely($userId)
    {
        try {
            // Clear session-based cart if user is currently logged in
            if (auth()->id() == $userId) {
                $this->checkoutService->clearUserCart($userId);
                
                // Also clear any session cart data
                session()->forget([
                    'cart',
                    'cart_items',
                    'shopping_cart'
                ]);
            }

            // If you have database-based cart, clear it here
            // Example:
            // \App\Models\CartItem::where('user_id', $userId)->delete();
            
            Log::info('User cart cleared completely', ['user_id' => $userId]);
            
        } catch (\Exception $e) {
            Log::error('Error clearing user cart:', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Show success page after payment
     */
    public function success(Request $request): View
    {
        $sessionId = $request->get('session_id');
        $order = null;

        if ($sessionId) {
            // Try to find the order by stripe session
            $payment = Payment::where('stripe_session_id', $sessionId)->first();
            if ($payment && $payment->order) {
                $order = $payment->order->load(['orderItems.product', 'country', 'city']);
            }
        }

        // If no order found but user is logged in, get their latest order
        if (!$order && auth()->check()) {
            $order = Order::where('client_id', auth()->id())
                         ->latest()
                         ->with(['orderItems.product', 'country', 'city'])
                         ->first();
        }

        return view('frontend.checkout.success', compact('order'));
    }

    /**
     * Cancel page (when user cancels Stripe checkout)
     */
    public function cancel(): View
    {
        return view('frontend.checkout.cancel')
            ->with('message', 'Payment was cancelled. Your cart items are still saved.');
    }
}