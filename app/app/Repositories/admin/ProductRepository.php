<?php

namespace App\Repositories\Admin;

use App\Models\Product;
use App\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductRepository extends BaseRepository
{
    protected function model(): string
    {
        return Product::class;
    }

    /**
     * Get paginated products with optional filters, eager loading & sorting.
     */
    public function getProductsPaginate(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        $query = $this->model->with(['category', 'images']);

        // 🔍 Search by title or description
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'LIKE', "%{$filters['search']}%")
                  ->orWhere('description', 'LIKE', "%{$filters['search']}%");
            });
        }

        // 📂 Filter by categories
        $categoryIds = $filters['category_ids'] ?? [];
        if (!is_array($categoryIds)) {
            $categoryIds = [$categoryIds];
        }
        $categoryIds = array_values(array_filter($categoryIds));

        if (!empty($categoryIds)) {
            $query->whereIn('category_id', $categoryIds);
        }

        // 💰 Filter by price range
        $min = $filters['min'] ?? 0;
        $max = $filters['max'] ?? 500;
        $query->whereBetween('price', [$min, $max]);

        // 📊 Apply sorting
        $sort = $filters['sort'] ?? 'default';
        switch ($sort) {
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc'); // default fallback
        }

        return $query->paginate($perPage);
    }

    public function getLatestProducts(int $perPage = 10)
    {
        return $this->model
            ->with(['category', 'images'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getFeaturedProducts(int $limit = 8)
    {
        return $this->model
            ->with(['category', 'images'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}