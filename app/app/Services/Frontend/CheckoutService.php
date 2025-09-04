<?php

namespace App\Services\Frontend;

use App\Services\Frontend\CartService;
use App\Services\Frontend\ClientService;
use App\Services\Frontend\OrderService;
use App\Services\Frontend\PaymentService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Exception;

class CheckoutService
{
    public function __construct(
        private CartService $cartService,
        private ClientService $clientService,
        private OrderService $orderService,
        private PaymentService $paymentService
    ) {}

    /** Get cart data for checkout */
    public function getCartData(): array
    {
        $items = $this->cartService->getItems();

        return [
            'items' => $items->values(),
            'total' => $items->sum(fn($i) => $i['price'] * $i['quantity']),
            'count' => $items->sum(fn($i) => $i['quantity']),
            'is_empty' => $items->isEmpty(),
        ];
    }

    /** Place order (non-Stripe flow) */
    public function placeOrder(array $data)
    {
        $user = auth()->user();
        if (!$user) throw new Exception("User must be logged in");

        $items = $this->cartService->getItems()->toArray();
        if (empty($items)) throw new Exception("Cart is empty");

        $client = $this->clientService->getOrCreateClient($user, $data);

        $totalAmount = collect($items)->sum(fn($item) => $item['price'] * $item['quantity']);

        $payment = $this->paymentService->createPayment([
            'amount' => $totalAmount,
            'method' => 'pending',
            'status' => 'pending',
        ]);

        return $this->orderService->createOrder($client, $payment, $items, $totalAmount);
    }

    /** Create Stripe checkout session */
    public function createStripeSession(array $data)
    {
        $user = auth()->user();
        if (!$user) throw new Exception("User must be logged in");

        $items = $this->cartService->getItems();
        if ($items->isEmpty()) throw new Exception("Cart is empty");

        Stripe::setApiKey(config('stripe.secret'));

        $lineItems = $items->map(fn($item) => [
            'price_data' => [
                'currency' => 'usd',
                'product_data' => [
                    'name' => $item['title'],
                    'description' => $item['description'] ?? null
                ],
                'unit_amount' => intval($item['price'] * 100),
            ],
            'quantity' => intval($item['quantity'])
        ])->toArray();

        $sessionKey = 'stripe_checkout_' . uniqid();
        Session::put($sessionKey, [
            'user_id' => $user->id,
            'cart_items' => $items->toArray(),
            'client_data' => $data,
            'created_at' => now()->timestamp
        ]);

        $session = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('checkout.index'),
            'metadata' => ['session_key' => $sessionKey],
            'customer_email' => $user->email,
        ]);

        Log::info('Stripe session created', ['session_id' => $session->id]);

        return $session;
    }

    /** Retrieve order from Stripe session */
    public function getOrderFromStripeSession(?string $sessionId)
    {
        if (!$sessionId) return null;

        try {
            Stripe::setApiKey(config('stripe.secret'));
            $session = StripeSession::retrieve($sessionId);

            return \App\Models\Order::where('payment_id', $session->payment_intent)->first();
        } catch (\Exception $e) {
            Log::error('Error retrieving Stripe session', [
                'session_id' => $sessionId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}