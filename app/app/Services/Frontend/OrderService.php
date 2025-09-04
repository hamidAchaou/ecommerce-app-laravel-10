<?php

namespace App\Services\Frontend;

use App\Models\Client;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class OrderService
{
    public function __construct(
        protected PaymentService $paymentService,
        protected ClientService $clientService,
        protected CartService $cartService
    ) {}

    /**
     * Create order from Stripe session
     */
    public function createOrderFromStripeSession(object $session): Order
    {
        $userId = $session->metadata->user_id ?? null;
        $sessionKey = $session->metadata->session_key ?? null;

        if (!$userId || !$sessionKey) {
            throw new \InvalidArgumentException("Invalid Stripe session metadata.");
        }

        $user = \App\Models\User::findOrFail($userId);

        $sessionData = Session::get($sessionKey, null);
        if (!$sessionData) {
            throw new \Exception("Session data not found for key: {$sessionKey}");
        }

        $items = $sessionData['cart_items'] ?? [];
        $clientData = $sessionData['client_data'] ?? [];
        $totalAmount = $sessionData['total_amount'] ?? 0;

        if (empty($items) || empty($clientData)) {
            throw new \Exception("Cart items or client data missing in session.");
        }

        return DB::transaction(function () use ($user, $items, $clientData, $totalAmount, $session, $sessionKey) {
            // 1️⃣ Client
            $client = $this->clientService->getOrCreateClient($user, $clientData);

            // 2️⃣ Payment
            $payment = $this->paymentService->createPayment([
                'amount'         => $totalAmount,
                'method'         => 'stripe',
                'status'         => 'completed',
                'transaction_id' => $session->payment_intent,
                'metadata'       => json_encode([
                    'stripe_session_id'   => $session->id,
                    'stripe_payment_intent' => $session->payment_intent,
                ])
            ]);

            // 3️⃣ Order
            $order = $this->createOrder($client, $payment, $items, $totalAmount);

            // 4️⃣ Mark as paid
            $order->update(['status' => 'paid']);

            // 5️⃣ Clean session & cart
            Session::forget($sessionKey);
            $this->cartService->clearCart();

            return $order;
        });
    }

    /**
     * Create an order with items
     */
    public function createOrder(Client $client, Payment $payment, array $items, float $totalAmount): Order
    {
        return DB::transaction(function () use ($client, $payment, $items, $totalAmount) {
            $order = Order::create([
                'client_id'    => $client->id,
                'payment_id'   => $payment->id,
                'total_amount' => $totalAmount,
                'status'       => 'pending',
            ]);

            foreach ($items as $item) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $item['id'],
                    'quantity'   => $item['quantity'],
                    'price'      => $item['price'],
                    'total'      => $item['price'] * $item['quantity'],
                ]);
            }

            return $order;
        });
    }

    /**
     * Retrieve order with relations
     */
    public function findOrder(int $orderId): Order
    {
        return Order::with(['client', 'payment', 'orderItems.product'])
                    ->findOrFail($orderId);
    }
}