<?php

namespace App\Repositories;

use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

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
     * Get paginated orders with filtering, search, sorting, and eager loading.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllWithRelations(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->with(['client.user', 'payment']);
        // dd($filters);
        // Search by ID, client name, or client email
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('id', $search)
                  ->orWhereHas('client.user', fn($q2) => $q2->where('name', 'like', "%$search%")
                                                          ->orWhere('email', 'like', "%$search%"));
            });
        }

        // Filter by payment status
        if (!empty($filters['payment_status'])) {
            $query->whereHas('payment', fn($q) => $q->where('status', $filters['payment_status']));
        }

        // Filter by order status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Sorting
        $orderBy = $filters['sort_by'] ?? 'created_at';
        $direction = $filters['sort_dir'] ?? 'desc';
        $query->orderBy($orderBy, $direction);

        return $query->paginate($perPage);
    }

    public function findWithRelations(int $orderId)
    {
        return $this->model->with(['client.user', 'payment', 'orderItems.product'])->findOrFail($orderId);
    }
}