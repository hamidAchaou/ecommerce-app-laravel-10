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
    public function getProductsPaginate(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->getAllPaginate(
            filters: $filters,
            with: ['category'],
            searchableFields: ['title', 'description'],
            perPage: $perPage
        );
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

    public function getBySlug(string $slug)
    {
        return $this->model
            ->with(['category'])
            ->where('slug', $slug)
            ->first();
    }
}
