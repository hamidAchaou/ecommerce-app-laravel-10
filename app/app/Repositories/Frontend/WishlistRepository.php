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

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function exists(int $user_id, int $product_id): bool
    {
        return $this->model->where(compact('user_id', 'product_id'))->exists();
    }

    public function getUserWishlist(int $user_id)
    {
        return $this->model
            ->where('user_id', $user_id)
            ->with('product')
            ->latest()
            ->get();
    }

    public function remove(int $user_id, int $product_id): int
    {
        return $this->model->where(compact('user_id', 'product_id'))->delete();
    }
}