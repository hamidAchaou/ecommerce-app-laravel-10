<?php

namespace App\Services\Frontend;

use App\Repositories\Frontend\CartRepository;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class CartService
{
    public function __construct(protected CartRepository $cartRepo) {}

    /**
     * Get all cart items
     */
    public function getItems(): Collection
    {
        if (Auth::check()) {
            return $this->cartRepo->getItems(Auth::id());
        }

        // Guest → load from session
        $cart = session()->get('cart', []);
        if (empty($cart)) return collect();

        $products = Product::whereIn('id', array_keys($cart))->get()->keyBy('id');

        return collect($cart)->map(function ($item, $productId) use ($products) {
            $product = $products->get($productId);
            if (!$product) return null;

            return [
                'id' => $product->id,
                'title' => $product->title,
                'quantity' => $item['quantity'],
                'price' => (float) $product->price,
                'image' => $product->mainImageUrl(),
                'product' => $product,
            ];
        })->filter();
    }

    /**
     * Get cart summary
     */
    public function getCartSummary(): array
    {
        $items = $this->getItems();
        return [
            'items' => $items,
            'total' => $items->sum(fn($item) => $item['price'] * $item['quantity']),
            'count' => $items->count(),
        ];
    }

    /**
     * Add product to cart
     */
    public function addToCart(int $productId, int $quantity)
    {
        if (Auth::check()) {
            $this->cartRepo->addItem($productId, $quantity, Auth::id());
            return;
        }

        // Guest → save in session
        $cart = session()->get('cart', []);
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $product = Product::findOrFail($productId);
            $cart[$productId] = [
                'quantity' => $quantity,
                'price' => (float) $product->price,
            ];
        }
        session()->put('cart', $cart);
    }

    /**
     * Update cart item quantity
     */
    public function updateCartItem(int $productId, int $quantity)
    {
        if (Auth::check()) {
            $this->cartRepo->updateItem($productId, $quantity, Auth::id());
            return;
        }

        $cart = session()->get('cart', []);
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] = $quantity;
            session()->put('cart', $cart);
        }
    }

    /**
     * Remove item from cart
     */
    public function removeCartItem(int $productId)
    {
        if (Auth::check()) {
            $this->cartRepo->removeItem($productId, Auth::id());
            return;
        }

        $cart = session()->get('cart', []);
        unset($cart[$productId]);
        session()->put('cart', $cart);
    }

    /**
     * Clear entire cart
     */
    public function clearCart()
    {
        if (Auth::check()) {
            $this->cartRepo->clear(Auth::id());
            return;
        }

        session()->forget('cart');
    }

    /**
     * Merge session cart to user after login
     */
    public function mergeSessionToUser()
    {
        if (!Auth::check()) return;
        $this->cartRepo->mergeSessionToUser(Auth::id());
    }    
}