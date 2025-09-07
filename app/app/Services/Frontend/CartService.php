<?php

namespace App\Services\Frontend;

use App\Repositories\Frontend\CartRepository;
use Illuminate\Support\Collection;

class CartService
{
    public function __construct(protected CartRepository $cartRepo) {}

    public function addToCart(int $productId, int $quantity): void
    {
        $this->cartRepo->addItem($productId, $quantity, auth()->id());
    }

    public function getItems(): Collection
    {
        return $this->cartRepo->getItems(auth()->id());
    }

    public function getTotal(): float
    {
        return $this->getItems()->sum(fn($item) => $item['price'] * $item['quantity']);
    }

    public function getCartSummary(): array
    {
        $items = $this->getItems();
        $total = $items->sum(fn($i) => $i['price'] * $i['quantity']);
        $count = $items->sum(fn($i) => $i['quantity']);

        return [
            'items'           => $items->values(),
            'count'           => $count,
            'total'           => $total,
            'formatted_total' => $this->formatPrice($total),
            'is_empty'        => $items->isEmpty(),
        ];
    }

    public function mergeGuestCartToUser(): void
    {
        if (!auth()->check()) return;

        $this->cartRepo->mergeSessionToUser(auth()->id());
    }

    public function updateCartItem(int $productId, int $quantity): void
    {
        $this->cartRepo->updateItem($productId, $quantity, auth()->id());
    }

    public function removeCartItem(int $productId): void
    {
        $this->cartRepo->removeItem($productId, auth()->id());
    }

    public function clearCart(): void
    {
        $this->cartRepo->clear(auth()->id());
    }

    protected function formatPrice(float $amount): string
    {
        return '$' . number_format($amount, 2);
    }
}