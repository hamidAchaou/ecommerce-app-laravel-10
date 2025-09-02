<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\CheckoutRequest;
use App\Services\Frontend\CheckoutService;
use App\Services\Frontend\ClientService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\Order;

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
        $user = auth()->user();
        $client = $user->client;

        return view('frontend.checkout.index', [
            'client' => $client,
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

            // Get authenticated user
            $user = auth()->user();

            // Find or create client for this user
            $client = $this->clientService->getClientByUser($user);

            if ($client) {
                // Update existing client
                $updateClient = $this->clientService->updateClient($client, $validator->validated());
            } else {
                // Create new client if not exists
                $updateClient = $this->clientService->getOrCreateClient($user, $validator->validated());
            }

            // --- Create order after client is ready ---
            $cartData = $this->checkoutService->getCartData();
            // Create a pending order (before Stripe confirmation)
            $orderService = app(\App\Services\Frontend\OrderService::class);
            
            $order = $orderService->createOrder(
                $updateClient,
                app(\App\Services\Frontend\PaymentService::class)->createPayment([
                    'amount' => $cartData['total'],
                    'method' => 'stripe',
                    'status' => 'pending',
                ]),
                $cartData['items']->toArray(), // 👈 convert collection to array
                $cartData['total']
            );
            
            // dd($order);

            // Save order ID to session for later matching
            session(['pending_order_id' => $order->id]);

            // Check Stripe configuration
            if (!config('stripe.key') || !config('stripe.secret')) {
                Log::error('Stripe configuration missing');
                return response()->json(['error' => 'Payment system not configured'], 500);
            }

            $session = $this->checkoutService->createStripeSession($validator->validated());

            return response()->json(['id' => $session->id]);
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            Log::error('Stripe Invalid Request:', [
                'message' => $e->getMessage(),
                'type' => get_class($e)
            ]);
            return response()->json(['error' => 'Invalid payment request: ' . $e->getMessage()], 400);
        } catch (\Stripe\Exception\AuthenticationException $e) {
            Log::error('Stripe Authentication Error:', [
                'message' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Payment system authentication failed'], 500);
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
     * Show success page after payment
     */
    public function success(Request $request): View
    {
        $sessionId = $request->query('session_id');
        $order = null;

        if ($sessionId) {
            // Try to find order by payment_id (which should match payment_intent from Stripe)
            try {
                \Stripe\Stripe::setApiKey(config('stripe.secret'));
                $session = \Stripe\Checkout\Session::retrieve($sessionId);

                if ($session->payment_intent) {
                    $order = Order::where('payment_id', $session->payment_intent)->first();
                }
            } catch (\Exception $e) {
                Log::error('Error retrieving Stripe session on success page', [
                    'session_id' => $sessionId,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return view('frontend.checkout.success', [
            'order' => $order,
            'sessionId' => $sessionId
        ]);
    }
}