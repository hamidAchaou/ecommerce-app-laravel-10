<?php

namespace App\Services\Frontend;

use App\Repositories\Frontend\CartRepository;

class CartService
{
    protected CartRepository $cartRepo;
    protected ?int $userId;

    public function __construct(CartRepository $cartRepo)
    {
        $this->cartRepo = $cartRepo;
        $this->userId = auth()->id();
    }

    public function getCartSummary(): array
    {
        $items = $this->cartRepo->getItems($this->userId);
        $total = $items->sum(fn($i) => $i['price'] * $i['quantity']);
        $count = $items->sum(fn($i) => $i['quantity']);

        return [
            'items'           => $items->values(),
            'count'           => $count,
            'total'           => $total,
            'formatted_total' => '$' . number_format($total, 2),
            'is_empty'        => $items->isEmpty(),
        ];        
    }

    public function addToCart(int $productId, int $quantity): void
    {
        $this->cartRepo->addItem($productId, $quantity, $this->userId);
    }

    public function updateCartItem(int $productId, int $quantity): void
    {
        $this->cartRepo->updateItem($productId, $quantity, $this->userId);
    }

    public function removeCartItem(int $productId): void
    {
        $this->cartRepo->removeItem($productId, $this->userId);
    }

    public function clearCart(): void
    {
        $this->cartRepo->clear($this->userId);
    }
}