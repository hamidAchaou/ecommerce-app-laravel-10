<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

trait CartManagement
{
    /**
     * Get cart items - unified method for both authenticated and guest users.
     * This method should be used across CartController and CheckoutController.
     *
     * @param Request|null $request
     * @return Collection
     */
    public function getCartItems(Request $request = null): Collection
    {
        if (!$request) {
            $request = request();
        }

        if ($user = auth()->user()) {
            // First, merge any session cart if it exists
            $sessionCart = collect($request->session()->get('cart', []));
            
            if ($sessionCart->isNotEmpty()) {
                try {
                    $userCart = $user->cart()->firstOrCreate(['client_id' => $user->id]);

                    foreach ($sessionCart as $item) {
                        $existingItem = $userCart->cartItems()->where('product_id', $item['id'])->first();

                        if ($existingItem) {
                            $newQuantity = min($existingItem->quantity + $item['quantity'], 99);
                            $existingItem->update(['quantity' => $newQuantity]);
                        } else {
                            $userCart->cartItems()->create([
                                'product_id' => $item['id'],
                                'quantity' => min($item['quantity'], 99)
                            ]);
                        }
                    }
                    
                    $request->session()->forget('cart');
                    $request->session()->save();
                    
                    Log::info('Session cart merged with user cart', [
                        'user_id' => $user->id,
                        'session_items_count' => $sessionCart->count()
                    ]);
                } catch (\Exception $e) {
                    Log::error('Error merging session cart', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Return items from database
            $userCart = $user->cart()->first();
            if (!$userCart) {
                return collect([]);
            }

            $dbCartItems = $userCart->cartItems()->with('product.images')->get();

            return $dbCartItems->map(fn($item) => [
                'id' => $item->product_id,
                'title' => $item->product->title ?? 'Unknown Product',
                'price' => (float) ($item->product->price ?? 0),
                'quantity' => (int) $item->quantity,
                'image' => $item->product ? $item->product->mainImageUrl() : asset('images/placeholders/product-placeholder.png'),
                'product' => $item->product, // Include full product object if needed
            ]);
        } else {
            // For guests, return session cart
            $sessionCart = collect($request->session()->get('cart', []));

            Log::info('Getting guest cart from session', [
                'session_id' => $request->session()->getId(),
                'cart_data' => $sessionCart->toArray()
            ]);

            return $sessionCart->map(function ($item) {
                // Verify product still exists and get fresh data
                $product = \App\Models\Product::with('images')->find($item['id']);

                if (!$product) {
                    Log::warning('Product not found in cart', ['product_id' => $item['id']]);
                    return null; // Filter out deleted products
                }

                return [
                    'id' => (int) $item['id'],
                    'title' => $product->title,
                    'price' => (float) $product->price,
                    'quantity' => (int) $item['quantity'],
                    'image' => $product->mainImageUrl(),
                    'product' => $product, // Include full product object
                ];
            })->filter(); // Remove null entries
        }
    }

    /**
     * Calculate the total amount of the cart.
     *
     * @param Collection $cartItems
     * @return float
     */
    public function calculateCartTotal(Collection $cartItems): float
    {
        return $cartItems->sum(function ($item) {
            $price = isset($item['price']) ? (float) $item['price'] : 0;
            $quantity = isset($item['quantity']) ? (int) $item['quantity'] : 1;
            return $price * $quantity;
        });
    }

    /**
     * Get cart count (total quantity of items).
     *
     * @param Collection $cartItems
     * @return int
     */
    public function getCartCount(Collection $cartItems): int
    {
        return $cartItems->sum(fn($item) => (int) ($item['quantity'] ?? 0));
    }

    /**
     * Clear the entire cart.
     *
     * @param Request|null $request
     * @return void
     */
    public function clearCart(Request $request = null): void
    {
        if (!$request) {
            $request = request();
        }

        if ($user = auth()->user()) {
            // Clear database cart for authenticated users
            $cart = $user->cart()->first();
            if ($cart) {
                $cart->cartItems()->delete();
                Log::info('Database cart cleared', ['user_id' => $user->id]);
            }
        } else {
            // Clear session cart for guests
            $request->session()->forget('cart');
            $request->session()->save();
            Log::info('Session cart cleared', ['session_id' => $request->session()->getId()]);
        }
    }

    /**
     * Check if cart is empty.
     *
     * @param Request|null $request
     * @return bool
     */
    public function isCartEmpty(Request $request = null): bool
    {
        $cartItems = $this->getCartItems($request);
        return $cartItems->isEmpty();
    }

    /**
     * Get cart summary data.
     *
     * @param Request|null $request
     * @return array
     */
    public function getCartSummary(Request $request = null): array
    {
        $cartItems = $this->getCartItems($request);
        $total = $this->calculateCartTotal($cartItems);
        $count = $this->getCartCount($cartItems);

        return [
            'items' => $cartItems->values(),
            'count' => $count,
            'total' => $total,
            'formatted_total' => '$' . number_format($total, 2),
            'is_empty' => $cartItems->isEmpty(),
        ];
    }
}