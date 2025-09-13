<?php

namespace App\Repositories\Frontend;

use App\Models\Wishlist;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class WishlistRepository extends BaseRepository
{
    protected function model(): string
    {
        return Wishlist::class;
    }

    /**
     * Add product to wishlist.
     */
    public function create(array $data): Wishlist
    {
        return $this->model->create($data);
    }

    /**
     * Check if product already exists in wishlist.
     */
    public function exists(int $userId, int $productId): bool
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('product_id', $productId)
            ->exists();
    }

    /**
     * Get all wishlist items for a user with eager-loaded products.
     */
    public function getUserWishlist(int $userId): Collection
    {
        return $this->model
            ->with(['product.images', 'product.category'])
            ->where('user_id', $userId)
            ->latest()
            ->get();
    }

    /**
     * Remove a product from the wishlist.
     */
    public function remove(int $userId, int $productId): int
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('product_id', $productId)
            ->delete();
    }
}