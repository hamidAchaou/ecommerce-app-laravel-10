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
     * Display the shopping cart.
     * Combines session cart (guest) with database cart (auth).
     */
    public function index()
    {
        $cartItems = $this->getCartItems();
        $subtotal = $cartItems->sum(fn($item) => $item['price'] * $item['quantity']);

        return view('frontend.cart.index', [
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
        ]);
    }

    /**
     * Add product to cart (guest or auth).
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = $this->productRepo->find($request->product_id);
        $quantity = (int) $request->quantity;

        if ($user = auth()->user()) {
            // Auth user → store in database
            $cart = $user->cart()->firstOrCreate(['client_id' => $user->id]);
            $cart->cartItems()->updateOrCreate(
                ['product_id' => $product->id],
                ['quantity' => DB::raw("quantity + $quantity")]
            );

            $cartCount = $cart->cartItems()->count();
        } else {
            // Guest → store in session
            $cart = Session::get('cart', []);
            if (isset($cart[$product->id])) {
                $cart[$product->id]['quantity'] += $quantity;
            } else {
                $cart[$product->id] = [
                    'id' => $product->id,
                    'title' => $product->title,
                    'price' => $product->price,
                    'quantity' => $quantity,
                ];
            }
            Session::put('cart', $cart);
            $cartCount = count($cart);
        }

        return response()->json([
            'message' => 'Product added to cart!',
            'cart_count' => $cartCount,
        ]);
    }

    /**
     * Update item quantity in cart.
     */
    public function update(Request $request, int $id)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);
        $quantity = (int) $request->quantity;

        if ($user = auth()->user()) {
            $cart = $user->cart()->first();
            if ($cart && $cartItem = $cart->cartItems()->where('product_id', $id)->first()) {
                $cartItem->update(['quantity' => $quantity]);
                return redirect()->route('cart.index')->with('success', 'Cart updated.');
            }
        } else {
            $cart = Session::get('cart', []);
            if (isset($cart[$id])) {
                $cart[$id]['quantity'] = $quantity;
                Session::put('cart', $cart);
                return redirect()->route('cart.index')->with('success', 'Cart updated.');
            }
        }

        return redirect()->route('cart.index')->with('error', 'Item not found in cart.');
    }

    /**
     * Remove item from cart.
     */
    public function destroy(int $id)
    {
        if ($user = auth()->user()) {
            $cart = $user->cart()->first();
            if ($cart && $cartItem = $cart->cartItems()->where('product_id', $id)->first()) {
                $cartItem->delete();
                return redirect()->route('cart.index')->with('success', 'Item removed.');
            }
        } else {
            $cart = Session::get('cart', []);
            if (isset($cart[$id])) {
                unset($cart[$id]);
                Session::put('cart', $cart);
                return redirect()->route('cart.index')->with('success', 'Item removed.');
            }
        }

        return redirect()->route('cart.index')->with('error', 'Item not found in cart.');
    }

    /**
     * Clear entire cart.
     */
    public function clear()
    {
        if ($user = auth()->user()) {
            $cart = $user->cart()->first();
            if ($cart) $cart->cartItems()->delete();
        }
        Session::forget('cart');

        return redirect()->route('cart.index')->with('success', 'Cart cleared.');
    }

    /**
     * Get cart items (merge guest session + user DB cart if authenticated)
     */
    protected function getCartItems()
    {
        // 1️⃣ Guest cart from session
        $sessionCart = collect(Session::get('cart', []));

        if ($user = auth()->user()) {
            // 2️⃣ Get user cart from database with products and images
            $userCart = $user->cart()->firstOrCreate(['client_id' => $user->id]);
            $dbCartItems = $userCart->cartItems()->with('product.images')->get();

            // 3️⃣ Merge session cart into user cart (one-time on login)
            if ($sessionCart->isNotEmpty()) {
                foreach ($sessionCart as $item) {
                    $userCart->cartItems()->updateOrCreate(
                        ['product_id' => $item['id']],
                        ['quantity' => DB::raw("quantity + {$item['quantity']}")]
                    );
                }
                Session::forget('cart');
                $dbCartItems = $userCart->cartItems()->with('product.images')->get();
            }

            // 4️⃣ Map database cart to array with image URLs
            return $dbCartItems->map(fn($item) => [
                'id'       => $item->product_id,
                'title'    => $item->product->title,
                'price'    => $item->product->price,
                'quantity' => $item->quantity,
                'image'    => $item->product->mainImageUrl(),
            ]);
        }

        // 5️⃣ Map guest session cart to include images
        return $sessionCart->map(function ($item) {
            $product = \App\Models\Product::with('images')->find($item['id']);
            $item['image'] = $product ? $product->mainImageUrl() : asset('images/placeholders/product-placeholder.png');
            return $item;
        });
    }
}