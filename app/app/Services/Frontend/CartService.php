<?php

namespace App\Services\Frontend;

use App\Repositories\Frontend\CartRepository;
use Illuminate\Support\Collection;

class CartService
{
    public function __construct(protected CartRepository $cartRepo) {}

    public function getItems(): Collection
    {
        return $this->cartRepo->getItems(auth()->id());
    }

    public function getTotal(): float
    {
        return $this->cartRepo->getTotal(auth()->id());
    }

    public function getCartSummary(): array
    {
        $items = $this->getItems();
        return [
            'items' => $items,
            'total' => $items->sum(fn($item) => $item['price'] * $item['quantity']),
            'count' => $items->count(),
        ];
    }

    public function addToCart(int $productId, int $quantity)
    {
        $this->cartRepo->addItem($productId, $quantity, auth()->id());
    }

    public function updateCartItem(int $productId, int $quantity)
    {
        $this->cartRepo->updateItem($productId, $quantity, auth()->id());
    }

    public function removeCartItem(int $productId)
    {
        $this->cartRepo->removeItem($productId, auth()->id());
    }

    public function clearCart()
    {
        $this->cartRepo->clear(auth()->id());
    }
}