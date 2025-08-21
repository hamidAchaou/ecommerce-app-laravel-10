<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Repositories\admin\ProductRepository;
use App\Traits\CartManagement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    use CartManagement;

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
        $cartItems = $this->getCartItems($request);
        $subtotal = $this->calculateCartTotal($cartItems);

        if ($request->wantsJson()) {
            return response()->json([
                'cartItems' => $cartItems->values(),
                'subtotal'  => $subtotal,
                'count'     => $this->getCartCount($cartItems),
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
            'quantity'   => 'required|integer|min:1|max:99',
        ]);

        $productId = (int) $request->product_id;
        $quantity = (int) $request->quantity;

        // Log the incoming request for debugging
        Log::info('Adding to cart', [
            'product_id' => $productId,
            'quantity' => $quantity,
            'user_id' => auth()->id(),
            'session_id' => $request->session()->getId()
        ]);

        $product = $this->productRepo->find($productId);

        if (!$product) {
            return response()->json([
                'message' => 'Product not found',
            ], 404);
        }

        if ($user = auth()->user()) {
            // Handle authenticated user cart
            try {
                DB::beginTransaction();
                
                $cart = $user->cart()->firstOrCreate(['client_id' => $user->id]);
                $existingItem = $cart->cartItems()->where('product_id', $productId)->first();

                if ($existingItem) {
                    $newQuantity = $existingItem->quantity + $quantity;
                    $existingItem->update(['quantity' => min($newQuantity, 99)]); // Cap at 99
                } else {
                    $cart->cartItems()->create([
                        'product_id' => $productId,
                        'quantity' => min($quantity, 99)
                    ]);
                }

                $cartCount = $cart->cartItems()->sum('quantity');
                
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error adding to database cart', ['error' => $e->getMessage()]);
                return response()->json(['message' => 'Failed to add item to cart'], 500);
            }
        } else {
            // Handle session-based cart for guests
            try {
                $cart = $request->session()->get('cart', []);
                
                Log::info('Current session cart', ['cart' => $cart]);

                if (isset($cart[$productId])) {
                    $cart[$productId]['quantity'] = min($cart[$productId]['quantity'] + $quantity, 99);
                } else {
                    $cart[$productId] = [
                        'id' => $productId,
                        'title' => $product->title,
                        'price' => (float) $product->price,
                        'quantity' => min($quantity, 99),
                        'image' => $product->mainImageUrl(),
                    ];
                }

                $request->session()->put('cart', $cart);
                $request->session()->save(); // Force session save
                
                Log::info('Updated session cart', ['cart' => $cart]);
                
                $cartCount = array_sum(array_column($cart, 'quantity'));
            } catch (\Exception $e) {
                Log::error('Error adding to session cart', ['error' => $e->getMessage()]);
                return response()->json(['message' => 'Failed to add item to cart'], 500);
            }
        }

        return response()->json([
            'message' => 'Product added to cart successfully!',
            'cart_count' => $cartCount,
            'product' => [
                'id' => $product->id,
                'title' => $product->title,
                'price' => (float) $product->price
            ]
        ]);
    }

    /**
     * Update item quantity.
     */
    public function update(Request $request, int $id)
    {
        $request->validate(['quantity' => 'required|integer|min:1|max:99']);
        $quantity = (int) $request->quantity;

        if ($user = auth()->user()) {
            $cart = $user->cart()->first();
            if ($cart && $cartItem = $cart->cartItems()->where('product_id', $id)->first()) {
                $cartItem->update(['quantity' => $quantity]);
            } else {
                return response()->json(['message' => 'Item not found in cart'], 404);
            }
        } else {
            $cart = $request->session()->get('cart', []);
            if (isset($cart[$id])) {
                $cart[$id]['quantity'] = $quantity;
                $request->session()->put('cart', $cart);
                $request->session()->save();
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
            $cart = $request->session()->get('cart', []);
            if (!isset($cart[$id])) {
                return response()->json(['message' => 'Item not found in cart'], 404);
            }
            unset($cart[$id]);
            $request->session()->put('cart', $cart);
            $request->session()->save();
        }

        return response()->json(['message' => 'Item removed successfully!']);
    }

    /**
     * Clear all items from the cart.
     */
    public function clearAll(Request $request)
    {
        $this->clearCart($request);
    
        return response()->json([
            'message' => 'Cart cleared successfully'
        ]);
    }    

    /**
     * Get cart items count (useful for AJAX calls)
     */
    public function count(Request $request)
    {
        $cartItems = $this->getCartItems($request);
        $count = $this->getCartCount($cartItems);

        return response()->json(['count' => $count]);
    }

    /**
     * Merge session cart with user cart on login
     */
    public function mergeCarts(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        $sessionCart = collect($request->session()->get('cart', []));

        if ($sessionCart->isNotEmpty()) {
            try {
                DB::beginTransaction();
                
                $user = auth()->user();
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
                
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error merging carts', ['error' => $e->getMessage()]);
                return response()->json(['message' => 'Failed to merge carts'], 500);
            }
        }

        return response()->json(['message' => 'Carts merged successfully']);
    }
}