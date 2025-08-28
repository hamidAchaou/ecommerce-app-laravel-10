<?php

namespace App\Repositories\Frontend;

use App\Models\Cart;
use App\Models\Product;
use App\Repositories\BaseRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;

class CartRepository extends BaseRepository
{
    protected const MAX_QUANTITY = 99;

    protected function model(): string
    {
        return Cart::class;
    }

    /**
     * Get cart items for user or guest.
     */
    public function getItems(?int $userId = null): Collection
    {
        if ($userId) {
            $cart = $this->model->where('client_id', $userId)->first();

            if (!$cart) {
                return collect();
            }

            return $cart->cartItems()
                ->with('product.images')
                ->get()
                ->map(fn($item) => $this->mapItem($item->product, $item->quantity));
        }

        // Guest cart from session
        $sessionCart = Session::get('cart', []);
        $products = Product::with('images')
            ->whereIn('id', array_keys($sessionCart))
            ->get()
            ->keyBy('id');

        return collect($sessionCart)->map(function ($item, $productId) use ($products) {
            $product = $products->get($productId);
            return $product ? $this->mapItem($product, $item['quantity']) : null;
        })->filter();
    }

    /**
     * Add item to cart.
     */
    public function addItem(int $productId, int $quantity, ?int $userId = null): void
    {
        $quantity = min($quantity, self::MAX_QUANTITY);
        if ($userId) {
            // Ensure cart exists
            $cart = $this->model->firstOrCreate(['client_id' => $userId]);

            // Check if product already exists
            $existing = $cart->cartItems()->where('product_id', $productId)->first();
            $newQty   = $existing
                ? min($existing->quantity + $quantity, self::MAX_QUANTITY)
                : $quantity;

            // Save (Laravel auto-fills cart_id when called via relation)
            $cart->cartItems()->updateOrCreate(
                ['product_id' => $productId],
                ['quantity' => $newQty]
            );

            return;
        }

        // Guest (session)
        $cart = Session::get('cart', []);
        $cart[$productId]['quantity'] = isset($cart[$productId])
            ? min($cart[$productId]['quantity'] + $quantity, self::MAX_QUANTITY)
            : $quantity;

        Session::put('cart', $cart);
    }

    /**
     * Update item quantity.
     */
    public function updateItem(int $productId, int $quantity, ?int $userId = null): void
    {
        $quantity = min($quantity, self::MAX_QUANTITY);

        if ($userId) {
            $this->model->where('client_id', $userId)
                ->first()?->cartItems()
                ->where('product_id', $productId)
                ->update(['quantity' => $quantity]);
            return;
        }

        $cart = Session::get('cart', []);
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] = $quantity;
            Session::put('cart', $cart);
        }
    }

    /**
     * Remove item from cart.
     */
    public function removeItem(int $productId, ?int $userId = null): void
    {
        if ($userId) {
            $this->model->where('client_id', $userId)
                ->first()?->cartItems()
                ->where('product_id', $productId)
                ->delete();
            return;
        }

        $cart = Session::get('cart', []);
        unset($cart[$productId]);
        Session::put('cart', $cart);
    }

    /**
     * Clear the cart.
     */
    public function clear(?int $userId = null): void
    {
        if ($userId) {
            $this->model->where('client_id', $userId)
                ->first()?->cartItems()
                ->delete();
        } else {
            Session::forget('cart');
        }
    }

    /**
     * Calculate total.
     */
    public function getTotal(?int $userId = null): float
    {
        return $this->getItems($userId)
            ->sum(fn($item) => $item['price'] * $item['quantity']);
    }

    /**
     * Normalize product + quantity into array.
     */
    protected function mapItem(Product $product, int $quantity): array
    {
        return [
            'id'       => $product->id,
            'title'    => $product->title,
            'price'    => (float) $product->price,
            'quantity' => (int) $quantity,
            'image'    => $product->mainImageUrl(),
            'product'  => $product,
        ];
    }
}