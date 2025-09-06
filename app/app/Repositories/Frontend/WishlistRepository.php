<?php

namespace App\Repositories\Frontend;

use App\Models\Wishlist;
use App\Repositories\BaseRepository;

class WishlistRepository extends BaseRepository
{
    protected function model(): string
    {
        return Wishlist::class;
    }

    // Check if product already exists in wishlist
    public function exists(int $userId, int $productId): bool
    {
        return $this->model->where('user_id', $userId)
            ->where('product_id', $productId)
            ->exists();
    }

    // Get all wishlist items for a user with product data
    public function getUserWishlist(int $userId)
    {
        return $this->model->where('user_id', $userId)
            ->with('product')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    // Remove product from wishlist
    public function remove(int $userId, int $productId)
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('product_id', $productId)
            ->delete();
    }
}