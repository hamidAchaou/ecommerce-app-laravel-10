<?php

namespace App\Services\Frontend;

use App\Services\Frontend\CartService;
use App\Services\Frontend\ClientService;
use App\Services\Frontend\OrderService;
use App\Services\Frontend\PaymentService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class CheckoutService
{
    public function __construct(
        private CartService $cartService,
        private ClientService $clientService,
        private OrderService $orderService,
        private PaymentService $paymentService
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

    /**
     * Get or create client for the authenticated user
     */
    private function getOrCreateClient(array $clientData)
    {
        $user = $this->getUser();
        if (!$user) {
            throw new \Exception("User must be logged in");
        }

        // Get existing client or create new one
        $client = $this->clientService->getOrCreateClient($user, $clientData);
        
        return $client;
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

        // Validate Stripe configuration
        $stripeSecret = config('stripe.secret');
        if (!$stripeSecret) {
            Log::error('Stripe secret key not configured');
            throw new \Exception("Payment system not configured");
        }

        try {
            // Set Stripe API key
            \Stripe\Stripe::setApiKey($stripeSecret);

            // Calculate total
            $totalAmount = $items->sum(fn($item) => $item['price'] * $item['quantity']);

            // Store necessary data in session for webhook processing
            $sessionData = [
                'user_id' => $user->id,
                'cart_items' => $items->toArray(),
                'client_data' => [
                    'name' => $data['name'],
                    'phone' => $data['phone'],
                    'address' => $data['address'],
                    'country_id' => $data['country_id'],
                    'city_id' => $data['city_id'],
                    'notes' => $data['notes'] ?? null,
                ],
                'total_amount' => $totalAmount,
                'created_at' => now()->timestamp
            ];
            
            // Generate unique session key
            $sessionKey = 'stripe_checkout_' . uniqid();
            Session::put($sessionKey, $sessionData);

            // Prepare line items for Stripe
            $lineItems = $items->map(function ($item) {
                $unitAmount = intval(floatval($item['price']) * 100); // Convert to cents
                
                if ($unitAmount <= 0) {
                    throw new \Exception("Invalid item price: " . $item['price']);
                }

                return [
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => $item['title'],
                            'description' => $item['description'] ?? null,
                        ],
                        'unit_amount' => $unitAmount,
                    ],
                    'quantity' => intval($item['quantity']),
                ];
            })->toArray();

            // Create Stripe session
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('checkout.index'),
                'metadata' => [
                    'user_id' => $user->id,
                    'session_key' => $sessionKey,
                    'total_amount' => $totalAmount,
                ],
                'customer_email' => $user->email ?? null,
                'billing_address_collection' => 'auto',
                'shipping_address_collection' => [
                    'allowed_countries' => ['US', 'CA', 'GB', 'FR', 'DE', 'MA'],
                ],
            ]);

            Log::info('Stripe session created successfully', [
                'session_id' => $session->id,
                'user_id' => $user->id,
                'session_key' => $sessionKey,
                'total_amount' => $totalAmount
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
     * Place order directly (non-Stripe flow)
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

        // Create or update client information
        $client = $this->getOrCreateClient([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'country_id' => $data['country_id'],
            'city_id' => $data['city_id'],
            'notes' => $data['notes'] ?? null,
        ]);

        // Calculate total
        $totalAmount = collect($items)->sum(fn($item) => $item['price'] * $item['quantity']);

        // Create payment record
        $payment = $this->paymentService->createPayment([
            'amount' => $totalAmount,
            'method' => 'pending', // or whatever default method
            'status' => 'pending',
        ]);

        // Create order
        return $this->orderService->createOrder($client, $payment, $items, $totalAmount);
    }
}