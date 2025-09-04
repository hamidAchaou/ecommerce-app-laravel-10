<?php

namespace App\Repositories;

use App\Models\Order;

/**
 * Repository for Order model
 *
 * Handles all database interactions related to Orders.
 */
class OrderRepository extends BaseRepository
{
    /**
     * Specify the model class name.
     *
     * @return string
     */
    protected function model(): string
    {
        return Order::class;
    }

    /**
     * Get all orders for a specific client with optional pagination.
     *
     * @param int $clientId
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getOrdersByClient(int $clientId, int $perPage = 15)
    {
        return $this->getAllPaginate(
            filters: ['client_id' => $clientId],
            with: ['payment', 'orderItems.product'],
            perPage: $perPage
        );
    }

    /**
     * Find a specific order with all relationships.
     *
     * @param int $orderId
     * @return \App\Models\Order
     */
    public function findWithRelations(int $orderId): Order
    {
        return $this->find($orderId, ['client', 'payment', 'orderItems.product']);
    }
}