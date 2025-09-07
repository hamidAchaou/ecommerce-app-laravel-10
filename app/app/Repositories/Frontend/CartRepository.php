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

    public function getItems(?int $userId = null): Collection
    {
        if ($userId) {
            $cart = $this->model->where('client_id', $userId)->first();
            if (!$cart) return collect();

            return $cart->cartItems()->with('product.images')->get()
                ->map(fn($item) => $this->mapItem($item->product, $item->quantity));
        }

        $sessionCart = Session::get('cart', []);
        $products = Product::with('images')->whereIn('id', array_keys($sessionCart))->get()->keyBy('id');

        return collect($sessionCart)->map(
            fn($item, $productId) =>
            $products->get($productId) ? $this->mapItem($products[$productId], $item['quantity']) : null
        )->filter();
    }

    public function addItem(int $productId, int $quantity, ?int $userId = null): void
    {
        $quantity = min($quantity, self::MAX_QUANTITY);

        if ($userId) {
            $cart = $this->model->firstOrCreate(['client_id' => $userId]);
            $existing = $cart->cartItems()->where('product_id', $productId)->first();
            $newQty = $existing ? min($existing->quantity + $quantity, self::MAX_QUANTITY) : $quantity;

            $cart->cartItems()->updateOrCreate(
                ['product_id' => $productId],
                ['quantity' => $newQty]
            );
            return;
        }

        $cart = Session::get('cart', []);
        $cart[$productId]['quantity'] = isset($cart[$productId])
            ? min($cart[$productId]['quantity'] + $quantity, self::MAX_QUANTITY)
            : $quantity;
        Session::put('cart', $cart);
    }

    public function updateItem(int $productId, int $quantity, ?int $userId = null): void
    {
        $quantity = min($quantity, self::MAX_QUANTITY);

        if ($userId) {
            $this->model->where('client_id', $userId)->first()?->cartItems()
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

    public function removeItem(int $productId, ?int $userId = null): void
    {
        if ($userId) {
            $this->model->where('client_id', $userId)->first()?->cartItems()
                ->where('product_id', $productId)
                ->delete();
            return;
        }

        $cart = Session::get('cart', []);
        unset($cart[$productId]);
        Session::put('cart', $cart);
    }

    public function clear(?int $userId = null): void
    {
        if ($userId) {
            $this->model->where('client_id', $userId)->first()?->cartItems()->delete();
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
            'id' => $product->id,
            'title' => $product->title,
            'price' => (float) $product->price,
            'quantity' => (int) $quantity,
            'image' => $product->mainImageUrl(),
            'product' => $product,
        ];
    }


    public function mergeSessionToUser(int $userId): void
    {
        $sessionCart = Session::get('cart', []);
        if (empty($sessionCart)) return;

        $cart = $this->model->firstOrCreate(['client_id' => $userId]);

        foreach ($sessionCart as $productId => $item) {
            $existing = $cart->cartItems()->where('product_id', $productId)->first();
            $newQty = $existing
                ? min($existing->quantity + $item['quantity'], self::MAX_QUANTITY)
                : min($item['quantity'], self::MAX_QUANTITY);

            $cart->cartItems()->updateOrCreate(
                ['product_id' => $productId],
                ['quantity' => $newQty]
            );
        }

        Session::forget('cart');
    }
}