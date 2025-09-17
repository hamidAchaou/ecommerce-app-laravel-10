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

    public function findByStripeSession(string $sessionId): ?Order
    {
        return $this->model
            ->with(['orderItems.product', 'client.user'])
            ->where('stripe_session_id', $sessionId)
            ->first();
    }

    public function countAll(array $filters = []): int
    {
        return $filters ? $this->buildFilteredQuery($filters)->count() : $this->model->count();
    }

    public function sumAll(string $column, array $filters = []): float
    {
        return $filters ? $this->buildFilteredQuery($filters)->sum($column) : $this->model->sum($column);
    }

    public function getMonthlySales(): array
    {
        return $this->model
            ->selectRaw('MONTHNAME(created_at) as month, SUM(total_amount) as total')
            ->groupByRaw('MONTH(created_at), MONTHNAME(created_at)')
            ->orderByRaw('MIN(created_at)')
            ->pluck('total', 'month')
            ->toArray();
    }
}