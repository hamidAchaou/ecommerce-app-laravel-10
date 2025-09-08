<?php

namespace App\Services\Frontend;

use App\Models\Client;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\CartItem;
use App\Repositories\OrderRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService
{
    public function __construct(
        protected PaymentService $paymentService,
        protected ClientService $clientService,
        protected CartService $cartService,
        protected OrderRepository $orderRepo
    ) {}

    /**
     * Create order from Stripe session
     */
    public function createOrderFromStripeSession(object $session): Order
    {
        $userId = $session->metadata->user_id ?? null;
        $paymentIntentId = $session->payment_intent ?? null;

        if (!$userId || !$paymentIntentId) {
            throw new \InvalidArgumentException("Stripe session is missing user_id or payment_intent.");
        }

        $user = \App\Models\User::findOrFail($userId);

        // Retrieve cart items from Stripe metadata
        $items = [];
        if (!empty($session->metadata->cart_items)) {
            $items = json_decode($session->metadata->cart_items, true);
        }

        // Fallback to DB cart items
        if (empty($items)) {
            $items = CartItem::where('user_id', $userId)
                ->with('product')
                ->get()
                ->map(fn($item) => [
                    'id'       => $item->product_id,
                    'quantity' => $item->quantity,
                    'price'    => $item->product->price,
                ])
                ->toArray();
        }

        if (empty($items)) {
            throw new \Exception("No cart items found for user {$userId}.");
        }

        // Retrieve client data from Stripe metadata
        $clientData = [];
        if (!empty($session->metadata->client_data)) {
            $clientData = json_decode($session->metadata->client_data, true);
        }

        if (empty($clientData)) {
            throw new \Exception("Client data missing for user {$userId}.");
        }

        $totalAmount = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $items));

        return DB::transaction(function () use ($user, $items, $clientData, $totalAmount, $paymentIntentId, $session) {

            // 1️⃣ Get or create client
            $client = $this->clientService->getOrCreateClient($user, $clientData);

            // 2️⃣ Create payment record
            $payment = $this->paymentService->createPayment([
                'amount'         => $totalAmount,
                'method'         => 'stripe',
                'status'         => 'completed',
                'transaction_id' => $paymentIntentId,
                'metadata'       => json_encode([
                    'stripe_session_id'    => $session->id ?? null,
                    'stripe_payment_intent'=> $paymentIntentId,
                ]),
            ]);

            // 3️⃣ Create order using repository
            $order = $this->orderRepo->create([
                'client_id'    => $client->id,
                'payment_id'   => $payment->id,
                'total_amount' => $totalAmount,
                'status'       => 'pending',
            ]);

            // 4️⃣ Attach order items
            foreach ($items as $item) {
                $order->orderItems()->create([
                    'product_id' => $item['id'],
                    'quantity'   => $item['quantity'],
                    'price'      => $item['price'],
                    'total'      => $item['price'] * $item['quantity'],
                ]);
            }

            // 5️⃣ Mark order as paid
            $this->orderRepo->update(['status' => 'paid'], $order->id);

            // 6️⃣ Clear user's cart
            $this->cartService->clearCart($user->id);

            Log::info('Order created successfully from Stripe', [
                'order_id'     => $order->id,
                'user_id'      => $user->id,
                'payment_id'   => $payment->id,
                'total_amount' => $totalAmount,
            ]);

            return $order->fresh(['client', 'payment', 'orderItems.product']);
        });
    }

    /**
     * Find an order with relations
     */
    public function findOrder(int $orderId): Order
    {
        return $this->orderRepo->findWithRelations($orderId);
    }
}