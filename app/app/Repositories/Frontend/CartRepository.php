<?php

namespace App\Repositories\Frontend;

use App\Models\Cart;
use App\Models\Product;
use App\Repositories\BaseRepository;
use Illuminate\Support\Collection;

class CartRepository extends BaseRepository
{
    /**
     * Define the model that this repository works with.
     */
    protected function model(): string
    {
        return Cart::class;
    }

    /**
     * Get cart items for the given user or from session (guest).
     */
    public function getItems(?int $userId = null): Collection
    {
        if ($userId) {
            $cart = $this->model->where('client_id', $userId)
                ->first()?->cartItems()
                ->with('product.images')
                ->get();

            if (!$cart) {
                return collect([]);
            }

            return $cart->map(fn($item) => [
                'id'       => $item->product_id,
                'title'    => $item->product->title ?? 'Unknown Product',
                'price'    => (float) ($item->product->price ?? 0),
                'quantity' => (int) $item->quantity,
                'image'    => $item->product?->mainImageUrl(),
                'product'  => $item->product,
            ]);
        }

        // Guest cart from session
        $sessionCart = collect(session()->get('cart', []));
        return $sessionCart->map(function ($item) {
            $product = Product::with('images')->find($item['id']);
            if (!$product) {
                return null;
            }
            return [
                'id'       => $product->id,
                'title'    => $product->title,
                'price'    => (float) $product->price,
                'quantity' => (int) $item['quantity'],
                'image'    => $product->mainImageUrl(),
                'product'  => $product,
            ];
        })->filter();
    }

    public function addItem(int $productId, int $quantity, ?int $userId = null): void
    {
        if ($userId) {
            $cart = $this->model->firstOrCreate(['client_id' => $userId]);
            $existingItem = $cart->cartItems()->where('product_id', $productId)->first();

            if ($existingItem) {
                $existingItem->update([
                    'quantity' => min($existingItem->quantity + $quantity, 99),
                ]);
            } else {
                $cart->cartItems()->create([
                    'product_id' => $productId,
                    'quantity'   => min($quantity, 99),
                ]);
            }
        } else {
            $cart = session()->get('cart', []);
            if (isset($cart[$productId])) {
                $cart[$productId]['quantity'] = min($cart[$productId]['quantity'] + $quantity, 99);
            } else {
                $product = Product::findOrFail($productId);
                $cart[$productId] = [
                    'id'       => $product->id,
                    'title'    => $product->title,
                    'price'    => (float) $product->price,
                    'quantity' => min($quantity, 99),
                    'image'    => $product->mainImageUrl(),
                ];
            }
            session()->put('cart', $cart);
        }
    }

    public function updateItem(int $productId, int $quantity, ?int $userId = null): void
    {
        if ($userId) {
            $this->model->where('client_id', $userId)
                ->first()?->cartItems()
                ->where('product_id', $productId)
                ->update(['quantity' => $quantity]);
        } else {
            $cart = session()->get('cart', []);
            if (isset($cart[$productId])) {
                $cart[$productId]['quantity'] = $quantity;
                session()->put('cart', $cart);
            }
        }
    }

    public function removeItem(int $productId, ?int $userId = null): void
    {
        if ($userId) {
            $this->model->where('client_id', $userId)
                ->first()?->cartItems()
                ->where('product_id', $productId)
                ->delete();
        } else {
            $cart = session()->get('cart', []);
            unset($cart[$productId]);
            session()->put('cart', $cart);
        }
    }

    public function clear(?int $userId = null): void
    {
        if ($userId) {
            $this->model->where('client_id', $userId)
                ->first()?->cartItems()
                ->delete();
        } else {
            session()->forget('cart');
        }
    }
}