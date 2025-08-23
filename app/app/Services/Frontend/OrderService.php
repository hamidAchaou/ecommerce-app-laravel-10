<?php

namespace App\Services\Frontend;

use App\Models\Client;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService
{
    /**
     * Create an order
     *
     * @param array $data
     * @return Order
     */
    public function createOrder(Client $client, array $items, array $data = []): Order
    {
        return DB::transaction(function () use ($client, $items, $data) {
            $order = $client->orders()->create($data);

            foreach ($items as $item) {
                $order->orderItems()->create([
                    'product_id' => $item['id'],
                    'quantity'   => $item['quantity'],
                    'price'      => $item['price'],
                ]);
            }

            return $order;
        });
    }

    /**
     * Get an order by ID
     */
    public function findOrder(int $orderId): Order
    {
        return Order::with('orderItems.product')->findOrFail($orderId);
    }

}