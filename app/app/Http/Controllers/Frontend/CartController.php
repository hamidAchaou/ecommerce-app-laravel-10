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
                'cartItems' => $cartItems,
                'subtotal'  => $subtotal,
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

        $product  = $this->productRepo->find($request->product_id);
        $quantity = (int) $request->quantity;

        if ($user = auth()->user()) {
            $cart = $user->cart()->firstOrCreate(['client_id' => $user->id]);
            $cart->cartItems()->updateOrCreate(
                ['product_id' => $product->id],
                ['quantity' => DB::raw("quantity + $quantity")]
            );

            $cartCount = $cart->cartItems()->count();
        } else {
            $cart = Session::get('cart', []);
            $cart[$product->id]['id']       = $product->id;
            $cart[$product->id]['title']    = $product->title;
            $cart[$product->id]['price']    = $product->price;
            $cart[$product->id]['quantity'] = ($cart[$product->id]['quantity'] ?? 0) + $quantity;
            Session::put('cart', $cart);

            $cartCount = count($cart);
        }

        return response()->json([
            'message'    => 'Product added to cart!',
            'cart_count' => $cartCount,
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
            }
        } else {
            $cart = Session::get('cart', []);
            if (isset($cart[$id])) {
                $cart[$id]['quantity'] = $quantity;
                Session::put('cart', $cart);
            }
        }

        return response()->json(['message' => 'Cart updated.']);
    }

    /**
     * Remove item.
     */
    public function destroy(Request $request, int $id)
    {
        if ($user = auth()->user()) {
            $cart = $user->cart()->first();
            if ($cart) {
                $cart->cartItems()->where('product_id', $id)->delete();
            }
        } else {
            $cart = Session::get('cart', []);
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
        }
        Session::forget('cart');

        return response()->json(['message' => 'Cart cleared.']);
    }

    /**
     * Merge and get items.
     */
    protected function getCartItems()
    {
        $sessionCart = collect(Session::get('cart', []));

        if ($user = auth()->user()) {
            $userCart   = $user->cart()->firstOrCreate(['client_id' => $user->id]);
            $dbCartItems = $userCart->cartItems()->with('product.images')->get();

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

            return $dbCartItems->map(fn($item) => [
                'id'       => $item->product_id,
                'title'    => $item->product->title,
                'price'    => $item->product->price,
                'quantity' => $item->quantity,
                'image'    => $item->product->mainImageUrl(),
            ]);
        }

        return $sessionCart->map(function ($item) {
            $product      = \App\Models\Product::with('images')->find($item['id']);
            $item['image'] = $product ? $product->mainImageUrl() : asset('images/placeholders/product-placeholder.png');
            return $item;
        });
    }
}