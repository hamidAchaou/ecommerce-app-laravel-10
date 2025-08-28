<?php

namespace App\Services\Frontend;

use App\Repositories\Frontend\CartRepository;
use Illuminate\Support\Collection;

class CartService
{
    public function __construct(
        protected CartRepository $cartRepo
    ) {}

    /**
     * Get current authenticated user id (if any).
     */
    protected function userId(): ?int
    {
        return auth()->id();
    }

    /**
     * Get all cart items for the current user/session.
     */
    public function getItems(): Collection
    {
        return $this->cartRepo->getItems($this->userId());
    }

    /**
     * Calculate the cart total price.
     */
    public function getTotal(): float
    {
        return $this->getItems()
            ->sum(fn($item) => $item['price'] * $item['quantity']);
    }

    /**
     * Provide a summary of the cart (items, count, total, etc).
     */
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

    /**
     * Add a product to the cart.
     */
    public function addToCart(int $productId, int $quantity): void
    {
        $this->cartRepo->addItem($productId, $quantity, $this->userId());
    }

    /**
     * Update a product quantity in the cart.
     */
    public function updateCartItem(int $productId, int $quantity): void
    {
        $this->cartRepo->updateItem($productId, $quantity, $this->userId());
    }

    /**
     * Remove a product from the cart.
     */
    public function removeCartItem(int $productId): void
    {
        $this->cartRepo->removeItem($productId, $this->userId());
    }

    /**
     * Clear all items from the cart.
     */
    public function clearCart(): void
    {
        $this->cartRepo->clear($this->userId());
    }

    /**
     * Format a float price into currency.
     */
    protected function formatPrice(float $amount): string
    {
        return '$' . number_format($amount, 2);
    }
}