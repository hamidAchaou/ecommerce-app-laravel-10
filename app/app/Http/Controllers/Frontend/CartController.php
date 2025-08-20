<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Repositories\admin\ProductRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    protected ProductRepository $productRepo;

    public function __construct(ProductRepository $productRepo)
    {
        $this->productRepo = $productRepo;
    }

    /**
     * Display the shopping cart (JSON or View).
     */
    public function index(Request $request)
    {
        $cartItems = $this->getCartItems();
        $subtotal = $cartItems->sum(fn($item) => $item['price'] * $item['quantity']);

        if ($request->wantsJson()) {
            return response()->json([
                'cartItems' => $cartItems->values(),
                'subtotal'  => $subtotal,
                'count'     => $cartItems->count(),
            ]);
        }

        return view('frontend.cart.index', compact('cartItems', 'subtotal'));
    }

    /**
     * Add product to cart.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $product = $this->productRepo->find($request->product_id);
        $quantity = (int) $request->quantity;

        if (!$product) {
            return response()->json([
                'message' => 'Product not found',
            ], 404);
        }

        if ($user = auth()->user()) {
            // Handle authenticated user cart
            $cart = $user->cart()->firstOrCreate(['client_id' => $user->id]);

            $existingItem = $cart->cartItems()->where('product_id', $product->id)->first();

            if ($existingItem) {
                $existingItem->update([
                    'quantity' => $existingItem->quantity + $quantity
                ]);
            } else {
                $cart->cartItems()->create([
                    'product_id' => $product->id,
                    'quantity' => $quantity
                ]);
            }

            $cartCount = $cart->cartItems()->sum('quantity');
        } else {
            // Handle session-based cart for guests
            $cart = Session::get('cart', []);

            if (isset($cart[$product->id])) {
                $cart[$product->id]['quantity'] += $quantity;
            } else {
                $cart[$product->id] = [
                    'id' => $product->id,
                    'title' => $product->title,
                    'price' => $product->price,
                    'quantity' => $quantity,
                    'image' => $product->mainImageUrl(),
                ];
            }

            Session::put('cart', $cart);
            $cartCount = array_sum(array_column($cart, 'quantity'));
        }

        return response()->json([
            'message' => 'Product added to cart successfully!',
            'cart_count' => $cartCount,
            'product' => [
                'id' => $product->id,
                'title' => $product->title,
                'price' => $product->price
            ]
        ]);
    }

    /**
     * Update item quantity.
     */
    public function update(Request $request, int $id)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);
        $quantity = (int) $request->quantity;

        if ($user = auth()->user()) {
            $cart = $user->cart()->first();
            if ($cart && $cartItem = $cart->cartItems()->where('product_id', $id)->first()) {
                $cartItem->update(['quantity' => $quantity]);
            } else {
                return response()->json(['message' => 'Item not found in cart'], 404);
            }
        } else {
            $cart = Session::get('cart', []);
            if (isset($cart[$id])) {
                $cart[$id]['quantity'] = $quantity;
                Session::put('cart', $cart);
            } else {
                return response()->json(['message' => 'Item not found in cart'], 404);
            }
        }

        return response()->json(['message' => 'Cart updated successfully']);
    }

    /**
     * Remove item.
     */
    public function destroy(Request $request, int $id)
    {
        if ($user = auth()->user()) {
            $cart = $user->cart()->first();
            if ($cart) {
                $deleted = $cart->cartItems()->where('product_id', $id)->delete();
                if (!$deleted) {
                    return response()->json(['message' => 'Item not found in cart'], 404);
                }
            }
        } else {
            $cart = Session::get('cart', []);
            if (!isset($cart[$id])) {
                return response()->json(['message' => 'Item not found in cart'], 404);
            }
            unset($cart[$id]);
            Session::put('cart', $cart);
        }

        return response()->json(['message' => 'Item removed successfully!']);
    }

    /**
     * Clear all.
     */
    public function clear()
    {
        if ($user = auth()->user()) {
            $cart = $user->cart()->first();
            if ($cart) {
                $cart->cartItems()->delete();
            }
        } else {
            Session::forget('cart');
        }

        return response()->json(['message' => 'Cart cleared successfully']);
    }

    /**
     * Clear all items from the cart.
     * For authenticated users, it clears the database cart.
     * For guests, it clears the session cart.
     */
    public function clearAll(Request $request)
    {
        if ($request->user()) {
            // Authenticated user → clear from database
            DB::table('cart_items')
                ->where('user_id', $request->user()->id)
                ->delete();
        } else {
            // Guest user → clear session cart
            $request->session()->forget('cart');
        }

        return response()->json([
            'message' => 'Cart cleared successfully'
        ]);
    }

    /**
     * Get cart items count (useful for AJAX calls)
     */
    public function count()
    {
        $cartItems = $this->getCartItems();
        $count = $cartItems->sum('quantity');

        return response()->json(['count' => $count]);
    }

    /**
     * Merge session cart with user cart on login
     */
    public function mergeCarts()
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        $sessionCart = collect(Session::get('cart', []));

        if ($sessionCart->isNotEmpty()) {
            $user = auth()->user();
            $userCart = $user->cart()->firstOrCreate(['client_id' => $user->id]);

            foreach ($sessionCart as $item) {
                $existingItem = $userCart->cartItems()->where('product_id', $item['id'])->first();

                if ($existingItem) {
                    $existingItem->update([
                        'quantity' => $existingItem->quantity + $item['quantity']
                    ]);
                } else {
                    $userCart->cartItems()->create([
                        'product_id' => $item['id'],
                        'quantity' => $item['quantity']
                    ]);
                }
            }

            Session::forget('cart');
        }

        return response()->json(['message' => 'Carts merged successfully']);
    }

    /**
     * Merge and get items.
     */
    protected function getCartItems()
    {
        if ($user = auth()->user()) {
            // For authenticated users, merge session cart if exists, then return DB cart
            $sessionCart = collect(Session::get('cart', []));
            $userCart = $user->cart()->firstOrCreate(['client_id' => $user->id]);

            // Merge session cart into user cart if session cart exists
            if ($sessionCart->isNotEmpty()) {
                foreach ($sessionCart as $item) {
                    $existingItem = $userCart->cartItems()->where('product_id', $item['id'])->first();

                    if ($existingItem) {
                        $existingItem->update([
                            'quantity' => $existingItem->quantity + $item['quantity']
                        ]);
                    } else {
                        $userCart->cartItems()->create([
                            'product_id' => $item['id'],
                            'quantity' => $item['quantity']
                        ]);
                    }
                }
                Session::forget('cart');
            }

            // Return items from database
            $dbCartItems = $userCart->cartItems()->with('product.images')->get();

            return $dbCartItems->map(fn($item) => [
                'id' => $item->product_id,
                'title' => $item->product->title,
                'price' => (float) $item->product->price,
                'quantity' => (int) $item->quantity,
                'image' => $item->product->mainImageUrl(),
            ]);
        } else {
            // For guests, return session cart
            $sessionCart = collect(Session::get('cart', []));

            return $sessionCart->map(function ($item) {
                $product = \App\Models\Product::with('images')->find($item['id']);

                return [
                    'id' => (int) $item['id'],
                    'title' => $item['title'],
                    'price' => (float) $item['price'],
                    'quantity' => (int) $item['quantity'],
                    'image' => $product ? $product->mainImageUrl() : asset('images/placeholders/product-placeholder.png'),
                ];
            });
        }
    }
}