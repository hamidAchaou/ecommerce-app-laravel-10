<?php

namespace App\Repositories\admin;

use App\Models\Product;
use App\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductRepository extends BaseRepository
{
    /**
     * Specify the model class for the repository.
     *
     * @return string
     */
    protected function model(): string
    {
        return Product::class;
    }

    /**
     * Get paginated products with optional filters and eager loading.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getProductsPaginate(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        $query = $this->model->with('category');
    
        // Search by title or description
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'LIKE', "%{$filters['search']}%")
                  ->orWhere('description', 'LIKE', "%{$filters['search']}%");
            });
        }
    
        // Ensure category_ids is always an array
        $categoryIds = $filters['category_ids'] ?? [];
        if (!is_array($categoryIds)) {
            $categoryIds = [$categoryIds];
        }
    
        // Filter by multiple categories
        if (!empty($categoryIds) && !(count($categoryIds) === 1 && $categoryIds[0] === "")) {
            $query->whereIn('category_id', $categoryIds);
        }
    
        // Filter by price range
        $min = $filters['min'] ?? 0;
        $max = $filters['max'] ?? 500;
        $query->whereBetween('price', [$min, $max]);
    
        return $query->orderBy('created_at', 'desc')->paginate($perPage);
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