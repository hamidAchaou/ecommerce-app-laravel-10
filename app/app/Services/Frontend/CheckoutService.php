<?php

namespace App\Services\Frontend;

use App\Services\Frontend\CartService;
use App\Services\Frontend\ClientService;
use App\Services\Frontend\OrderService;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutService
{
    public function __construct(
        private CartService $cartService,
        private ClientService $clientService,
        private OrderService $orderService
    ) {}

    /**
     * Get cart data for checkout
     */
    public function getCartData(): array
    {
        $items = $this->cartService->getItems();
        return [
            'items'    => $items->values(),
            'total'    => $items->sum(fn($i) => $i['price'] * $i['quantity']),
            'count'    => $items->sum(fn($i) => $i['quantity']),
            'is_empty' => $items->isEmpty(),
        ];
    }

    /**
     * Get authenticated user
     */
    public function getUser()
    {
        return auth()->user();
    }

    public function createStripeSession(array $data)
    {
        // Validate user
        $user = $this->getUser();
        if (!$user) {
            throw new \Exception("User must be logged in to place an order");
        }

        // Validate cart
        $items = $this->cartService->getItems();
        if ($items->isEmpty()) {
            throw new \Exception("Cart is empty");
        }

        // Store cart items and order data in session with user-specific keys
        session([
            "pending_order_items_{$user->id}" => $items->toArray(),
            "pending_order_data_{$user->id}" => $data
        ]);

        // Validate Stripe configuration
        $stripeSecret = config('stripe.secret');
        if (!$stripeSecret) {
            Log::error('Stripe secret key not configured');
            throw new \Exception("Payment system not configured");
        }

        try {
            // Set Stripe API key
            \Stripe\Stripe::setApiKey($stripeSecret);

            // Prepare line items
            $lineItems = $items->map(function ($item) {
                // Validate item data
                if (!isset($item['title']) || !isset($item['price']) || !isset($item['quantity'])) {
                    throw new \Exception("Invalid cart item data");
                }

                $unitAmount = intval(floatval($item['price']) * 100); // Convert to cents
                
                if ($unitAmount <= 0) {
                    throw new \Exception("Invalid item price: " . $item['price']);
                }

                $productData = [
                    'name' => $item['title'],
                ];

                // Only add description if it exists and is not empty
                if (isset($item['description']) && !empty(trim($item['description']))) {
                    $productData['description'] = trim($item['description']);
                }

                return [
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => $productData,
                        'unit_amount' => $unitAmount,
                    ],
                    'quantity' => intval($item['quantity']),
                ];
            })->toArray();

            // Update success URL to include session_id parameter
            $successUrl = route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}';
            $cancelUrl = route('checkout.cancel');

            // Create Stripe session
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
                'metadata' => [
                    'user_id' => $user->id,
                    'user_name' => $data['name'] ?? '',
                    'user_phone' => $data['phone'] ?? '',
                    'user_address' => $data['address'] ?? '',
                    'country_id' => $data['country_id'] ?? '',
                    'city_id' => $data['city_id'] ?? '',
                    'notes' => $data['notes'] ?? '',
                ],
                'customer_email' => $user->email ?? null,
                'billing_address_collection' => 'auto',
                'shipping_address_collection' => [
                    'allowed_countries' => ['US', 'CA', 'GB', 'FR', 'DE', 'MA'],
                ],
                'expires_at' => now()->addMinutes(30)->timestamp, // Session expires in 30 minutes
            ]);

            Log::info('Stripe session created successfully', [
                'session_id' => $session->id,
                'user_id' => $user->id,
                'cart_total' => $items->sum(fn($i) => $i['price'] * $i['quantity'])
            ]);

            return $session;

        } catch (\Stripe\Exception\ApiErrorException $e) {
            Log::error('Stripe API Error:', [
                'message' => $e->getMessage(),
                'type' => get_class($e),
                'stripe_code' => $e->getStripeCode(),
            ]);
            throw new \Exception("Payment system error: " . $e->getMessage());
            
        } catch (\Exception $e) {
            Log::error('Stripe session creation failed:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data,
                'items_count' => $items->count()
            ]);
            throw $e;
        }
    }

    /**
     * Create order after successful payment
     */
    public function createOrderAfterPayment($stripeSession): Order
    {
        try {
            DB::beginTransaction();

            $userId = $stripeSession['metadata']['user_id'];
            $user = \App\Models\User::find($userId);

            if (!$user) {
                throw new \Exception("User not found");
            }

            // Get stored cart items and order data with user-specific keys
            $cartItems = session("pending_order_items_{$userId}", []);
            $orderData = session("pending_order_data_{$userId}", []);

            if (empty($cartItems)) {
                throw new \Exception("No cart items found for this session");
            }

            // Create Payment record
            $payment = Payment::create([
                'id' => Str::uuid(),
                'stripe_session_id' => $stripeSession['id'],
                'stripe_payment_intent_id' => $stripeSession['payment_intent'] ?? null,
                'amount' => $stripeSession['amount_total'] / 100,
                'currency' => $stripeSession['currency'],
                'status' => 'completed',
                'payment_method' => 'stripe',
            ]);

            // Calculate total
            $totalAmount = collect($cartItems)->sum(fn($item) => $item['price'] * $item['quantity']);

            // Create Order
            $order = Order::create([
                'id' => Str::uuid(),
                'client_id' => $userId,
                'status' => 'confirmed',
                'total_amount' => $totalAmount,
                'payment_id' => $payment->id,
                'shipping_name' => $orderData['name'] ?? $stripeSession['metadata']['user_name'],
                'shipping_phone' => $orderData['phone'] ?? $stripeSession['metadata']['user_phone'],
                'shipping_address' => $orderData['address'] ?? $stripeSession['metadata']['user_address'],
                'country_id' => $orderData['country_id'] ?? $stripeSession['metadata']['country_id'],
                'city_id' => $orderData['city_id'] ?? $stripeSession['metadata']['city_id'],
                'notes' => $orderData['notes'] ?? $stripeSession['metadata']['notes'],
            ]);

            // Create Order Items and reduce product quantities
            foreach ($cartItems as $item) {
                $product = Product::lockForUpdate()->find($item['id']); // Use row locking
                if (!$product) {
                    Log::warning('Product not found during order creation', ['product_id' => $item['id']]);
                    continue;
                }

                // Check available quantity
                $quantityToOrder = min(intval($item['quantity']), $product->quantity);

                if ($quantityToOrder > 0) {
                    // Create order item
                    OrderItem::create([
                        'id' => Str::uuid(),
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $quantityToOrder,
                        'price' => floatval($item['price']),
                        'total' => floatval($item['price']) * $quantityToOrder,
                    ]);

                    // Reduce product quantity
                    $product->decrement('quantity', $quantityToOrder);
                    
                    Log::info('Product quantity reduced', [
                        'product_id' => $product->id,
                        'quantity_reduced' => $quantityToOrder,
                        'remaining_quantity' => $product->fresh()->quantity
                    ]);
                }
            }

            // Clear all session data and cart
            $this->clearUserCartAndSession($userId);

            DB::commit();

            Log::info('Order created successfully after payment', [
                'order_id' => $order->id,
                'user_id' => $userId,
                'payment_id' => $payment->id
            ]);

            return $order;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create order after payment:', [
                'message' => $e->getMessage(),
                'stripe_session' => $stripeSession,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Clear user's cart and session data
     */
    public function clearUserCartAndSession($userId)
    {
        try {
            // Clear session-based cart if user is currently logged in
            if (auth()->id() == $userId) {
                $this->cartService->clear();
                
                // Clear all cart-related session data
                session()->forget([
                    'cart',
                    'cart_items',
                    'shopping_cart',
                    "pending_order_items_{$userId}",
                    "pending_order_data_{$userId}"
                ]);
            }

            // If you have database-based cart table, clear it here
            // Example:
            // \App\Models\CartItem::where('user_id', $userId)->delete();
            
            Log::info('User cart and session cleared', ['user_id' => $userId]);
            
        } catch (\Exception $e) {
            Log::error('Error clearing user cart and session:', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Clear user's cart (existing method)
     */
    public function clearUserCart($userId)
    {
        // Implementation depends on how your cart works
        // If using session-based cart:
        if (auth()->id() == $userId) {
            $this->cartService->clear();
        }
        
        // If using database cart, add appropriate logic here
        // Example:
        // \App\Models\CartItem::where('user_id', $userId)->delete();
    }

    /**
     * Place order (for non-Stripe checkout)
     */
    public function placeOrder(array $data)
    {
        $user = $this->getUser();
        if (!$user) {
            throw new \Exception("User must be logged in to place an order");
        }

        $items = $this->cartService->getItems()->toArray();
        
        if (empty($items)) {
            throw new \Exception("Cart is empty");
        }

        try {
            DB::beginTransaction();
            
            $order = $this->orderService->createOrder($user, $items, $data);
            
            // Clear cart after successful order creation
            $this->clearUserCartAndSession($user->id);
            
            DB::commit();
            
            return $order;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Validate product availability before checkout
     */
    public function validateCartAvailability(): array
    {
        $items = $this->cartService->getItems();
        $unavailableItems = [];
        $updatedItems = [];

        foreach ($items as $item) {
            $product = Product::find($item['id']);
            
            if (!$product) {
                $unavailableItems[] = $item['title'] . ' (product no longer exists)';
                continue;
            }

            if ($product->quantity < $item['quantity']) {
                if ($product->quantity == 0) {
                    $unavailableItems[] = $item['title'] . ' (out of stock)';
                } else {
                    $updatedItems[] = [
                        'title' => $item['title'],
                        'requested' => $item['quantity'],
                        'available' => $product->quantity
                    ];
                }
            }
        }

        return [
            'is_valid' => empty($unavailableItems) && empty($updatedItems),
            'unavailable_items' => $unavailableItems,
            'updated_items' => $updatedItems
        ];
    }
}