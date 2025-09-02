<?php

namespace App\Services\Frontend;

use App\Models\Client;
use App\Models\Payment;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class OrderService
{
    private PaymentService $paymentService;
    private ClientService $clientService;

    public function __construct(PaymentService $paymentService, ClientService $clientService)
    {
        $this->paymentService = $paymentService;
        $this->clientService = $clientService;
    }

    /**
     * Create order from Stripe session (webhook) - with extensive debugging
     */
    public function createOrderFromStripeSession($session)
    {
        Log::info('=== STARTING ORDER CREATION FROM STRIPE SESSION ===');
        
        $userId = $session->metadata->user_id ?? null;
        $sessionKey = $session->metadata->session_key ?? null;

        Log::info('Extracted session data', [
            'user_id' => $userId,
            'session_key' => $sessionKey,
            'session_metadata' => (array)$session->metadata
        ]);

        if (!$userId) {
            throw new \Exception("User ID missing in Stripe metadata");
        }

        // Find user
        $user = \App\Models\User::find($userId);
        if (!$user) {
            throw new \Exception("User not found with ID: {$userId}");
        }

        Log::info('User found', ['user_id' => $user->id, 'user_email' => $user->email]);

        // Get stored session data
        $sessionData = null;
        if ($sessionKey) {
            $sessionData = Session::get($sessionKey);
            Log::info('Session data retrieved', [
                'session_key' => $sessionKey,
                'session_data_exists' => !is_null($sessionData),
                'session_data' => $sessionData
            ]);
        }

        if (!$sessionData) {
            Log::error('Session data not found', ['session_key' => $sessionKey]);
            throw new \Exception("Session data not found for key: " . $sessionKey);
        }

        $items = $sessionData['cart_items'] ?? [];
        $clientData = $sessionData['client_data'] ?? [];
        $totalAmount = $sessionData['total_amount'] ?? 0;

        Log::info('Session data extracted', [
            'items_count' => count($items),
            'items' => $items,
            'client_data' => $clientData,
            'total_amount' => $totalAmount
        ]);

        if (empty($items)) {
            throw new \Exception("Cart items not found in session data");
        }

        if (empty($clientData)) {
            throw new \Exception("Client data not found in session data");
        }

        return DB::transaction(function () use ($user, $items, $clientData, $totalAmount, $session, $sessionKey) {
            Log::info('=== STARTING DATABASE TRANSACTION ===');

            try {
                // Step 1: Create or update client information
                Log::info('Creating/updating client...');
                $client = $this->clientService->getOrCreateClient($user, $clientData);
                Log::info('Client created/updated', [
                    'client_id' => $client->id,
                    'client_name' => $client->name
                ]);

                // Step 2: Create payment record for Stripe payment
                Log::info('Creating payment record...');
                $payment = $this->paymentService->createPayment([
                    'amount' => $totalAmount,
                    'method' => 'stripe',
                    'status' => 'completed',
                    'transaction_id' => $session->payment_intent,
                    'metadata' => json_encode([
                        'stripe_session_id' => $session->id,
                        'stripe_payment_intent' => $session->payment_intent,
                    ])
                ]);
                Log::info('Payment created', [
                    'payment_id' => $payment->id,
                    'transaction_id' => $payment->transaction_id
                ]);

                // Step 3: Create order
                Log::info('Creating order...');
                $order = $this->createOrder($client, $payment, $items, $totalAmount);
                Log::info('Order created', [
                    'order_id' => $order->id,
                    'status' => $order->status
                ]);

                // Update order status since payment is completed
                Log::info('Updating order status to paid...');
                $order->update(['status' => 'paid']);
                Log::info('Order status updated', ['new_status' => $order->fresh()->status]);

                // Step 4: Clean up session data
                Log::info('Cleaning up session data...');
                Session::forget($sessionKey);

                // Step 5: Clear user's cart
                Log::info('Clearing cart...');
                app(CartService::class)->clearCart();

                Log::info('=== ORDER CREATION COMPLETED SUCCESSFULLY ===', [
                    'order_id' => $order->id,
                    'client_id' => $client->id,
                    'payment_id' => $payment->id,
                    'session_id' => $session->id,
                    'user_id' => $userId
                ]);

                return $order;

            } catch (\Exception $e) {
                Log::error('=== DATABASE TRANSACTION FAILED ===', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }
        });
    }

    /**
     * Create an order with debugging
     */
    public function createOrder(Client $client, Payment $payment, array $items, float $totalAmount): Order
    {
        Log::info('=== CREATING ORDER RECORD ===', [
            'client_id' => $client->id,
            'payment_id' => $payment->id,
            'total_amount' => $totalAmount,
            'items_count' => count($items)
        ]);

        return DB::transaction(function () use ($client, $payment, $items, $totalAmount) {
            // Create order
            $order = Order::create([
                'client_id' => $client->id,
                'payment_id' => $payment->id,
                'total_amount' => $totalAmount,
                'status' => 'pending',
            ]);

            Log::info('Order record created', [
                'order_id' => $order->id,
                'client_id' => $order->client_id,
                'payment_id' => $order->payment_id,
                'total_amount' => $order->total_amount
            ]);

            // Create order items
            Log::info('Creating order items...');
            foreach ($items as $index => $item) {
                Log::info("Creating order item {$index}", [
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['price'] * $item['quantity']
                ]);

                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['price'] * $item['quantity'],
                ]);

                Log::info("Order item created", [
                    'order_item_id' => $orderItem->id,
                    'order_id' => $orderItem->order_id,
                    'product_id' => $orderItem->product_id
                ]);
            }

            Log::info('All order items created successfully', [
                'order_id' => $order->id,
                'items_created' => $order->orderItems()->count()
            ]);

            return $order;
        });
    }

    /**
     * Get an order by ID with relationships
     */
    public function findOrder(int $orderId): Order
    {
        return Order::with(['client', 'payment', 'orderItems.product'])
                   ->findOrFail($orderId);
    }
}