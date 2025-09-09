<?php

namespace App\Services\Frontend;

use App\Models\CartItem;
use App\Models\Order;
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
     * Handle Stripe checkout.session.completed webhook
     */
    public function handleCheckoutSessionCompleted(array $session): array
    {
        $metadata = (array) ($session['metadata'] ?? []);
        $paymentIntentId = $session['payment_intent'] ?? null;
        $userId = $metadata['user_id'] ?? null;

        if (!$userId || !$paymentIntentId) {
            Log::error('Stripe session missing user_id or payment_intent', ['session' => $session]);
            return ['status' => 'error', 'message' => 'Missing user_id or payment_intent'];
        }

        // Prevent duplicate order creation
        $existingOrder = \App\Models\Order::whereHas('payment', fn($q) => $q->where('transaction_id', $paymentIntentId))->first();
        if ($existingOrder) {
            Log::info('Stripe session already processed', ['payment_intent' => $paymentIntentId]);
            return ['status' => 'already_processed', 'order_id' => $existingOrder->id];
        }

        try {
            $order = $this->createOrderFromStripeSession((object)$session);
            return ['status' => 'success', 'order_id' => $order->id];
        } catch (\Exception $e) {
            Log::error('Failed to create order from Stripe session', [
                'error' => $e->getMessage(),
                'session' => $session,
            ]);
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Create order from Stripe session
     */
    public function createOrderFromStripeSession(object $session): Order
    {
        $metadata = (array) ($session->metadata ?? []);
        $userId = $metadata['user_id'] ?? null;
        $paymentIntentId = $session->payment_intent ?? null;

        if (!$userId || !$paymentIntentId) {
            throw new \InvalidArgumentException("Stripe session missing user_id or payment_intent.");
        }

        $user = \App\Models\User::findOrFail($userId);

        // 1️⃣ Get cart items from metadata
        $items = json_decode($metadata['cart_items'] ?? '[]', true);

        // 2️⃣ Fallback to DB cart items if metadata is empty
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

        // 3️⃣ Get client data from metadata
        $clientData = json_decode($metadata['client_data'] ?? '{}', true);
        if (empty($clientData)) {
            throw new \Exception("Client data missing for user {$userId}.");
        }

        $totalAmount = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $items));

        // 4️⃣ Use DB transaction
        return DB::transaction(function () use ($user, $items, $clientData, $totalAmount, $paymentIntentId, $session) {

            // Get or create client
            $client = $this->clientService->getOrCreateClient($user, $clientData);

            // Create payment record
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

            // Create order
            $order = $this->orderRepo->create([
                'client_id'    => $client->id,
                'payment_id'   => $payment->id,
                'total_amount' => $totalAmount,
                'status'       => 'pending',
            ]);

            // Attach order items
            foreach ($items as $item) {
                $order->orderItems()->create([
                    'product_id' => $item['id'],
                    'quantity'   => $item['quantity'],
                    'price'      => $item['price'],
                    'total'      => $item['price'] * $item['quantity'],
                ]);
            }

            // Mark order as paid
            $this->orderRepo->update(['status' => 'paid'], $order->id);

            // Clear user's cart
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
}