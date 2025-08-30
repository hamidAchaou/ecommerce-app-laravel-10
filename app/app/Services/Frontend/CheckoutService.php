<?php

namespace App\Services\Frontend;

use App\Services\Frontend\CartService;
use App\Services\Frontend\ClientService;
use App\Services\Frontend\OrderService;
use Illuminate\Support\Facades\Log;

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
     * Get authenticated user (changed from getClient to getUser)
     */
    public function getUser()
    {
        return auth()->user(); // Direct auth()->user() call
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

                // Only add description if it's not empty
                if (!empty($item['description'])) {
                    $productData['description'] = $item['description'];
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

            // Validate URLs
            $successUrl = route('checkout.success');
            $cancelUrl = route('checkout.index');

            // Create Stripe session
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
                'metadata' => [
                    'user_id' => $user->id, // Changed from client_id to user_id
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
                    'allowed_countries' => ['US', 'CA', 'GB', 'FR', 'DE', 'MA'], // Add your supported countries
                ],
            ]);

            Log::info('Stripe session created successfully', [
                'session_id' => $session->id,
                'user_id' => $user->id
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
     * Place order (updated to use User instead of Client)
     */
    public function placeOrder(array $data)
    {
        $user = $this->getUser();
        if (!$user) {
            throw new \Exception("User must be logged in to place an order");
        }

        // Convert items Collection to array
        $items = $this->cartService->getItems()->toArray();
        
        if (empty($items)) {
            throw new \Exception("Cart is empty");
        }

        return $this->orderService->createOrder($user, $items, $data);
    }
}