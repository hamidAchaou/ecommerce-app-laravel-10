<?php

namespace App\Services\Frontend;

use App\Models\Order;
use App\Repositories\OrderRepository;
use App\Services\Frontend\CartService;
use App\Services\Frontend\ClientService;
use App\Services\Frontend\OrderService;
use App\Services\Frontend\PaymentService;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Exception;
use Illuminate\Support\Facades\DB;

class CheckoutService
{
    public function __construct(
        private CartService $cartService,
        private ClientService $clientService,
        private OrderService $orderService,
        private PaymentService $paymentService,
        private OrderRepository $orderRepository
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
    public function createStripeSession(array $clientData)
    {
        $user = auth()->user();
        if (!$user) throw new Exception("User must be logged in");

        $items = $this->cartService->getItems();
        if ($items->isEmpty()) throw new Exception("Cart is empty");

        Stripe::setApiKey(config('services.stripe.secret'));

        // Prepare Stripe line items
        $lineItems = $items->map(fn($item) => [
            'price_data' => [
                'currency' => 'usd',
                'product_data' => [
                    'name' => $item['title'],
                    'description' => $item['description'] ?? null,
                ],
                'unit_amount' => intval($item['price'] * 100),
            ],
            'quantity' => intval($item['quantity']),
        ])->toArray();

        $session = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('checkout.index'),
            'customer_email' => $user->email,
        ]);

        // Store session info in DB (Stripe session ID + client/cart)
        DB::table('stripe_sessions')->insert([
            'session_id' => $session->id,
            'user_id' => $user->id,
            'cart_items' => json_encode($items->toArray()),
            'client_data' => json_encode($clientData),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Log::info('Stripe session created', [
            'session_id' => $session->id,
        ]);

        return $session;
    }

    /**
     * Retrieve order by Stripe session (stateless)
     */
    public function getOrderFromStripeSession(?string $sessionId)
    {
        if (empty($sessionId)) return null;

        try {
            return $this->orderRepository->findByStripeSession($sessionId);
        } catch (\Throwable $e) {
            Log::error('Error retrieving order for Stripe session', [
                'session_id' => $sessionId,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Retrieve the latest order for a given client
     */
    public function getLatestOrderForClient(?int $clientId): ?Order
    {
        if (!$clientId) {
            return null;
        }

        try {
            return Order::with(['orderItems.product', 'client.user'])
                        ->where('client_id', $clientId)
                        ->orderBy('created_at', 'desc')
                        ->first();
        } catch (\Throwable $e) {
            Log::error('Error retrieving latest order for client', [
                'client_id' => $clientId,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
}