<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\Frontend\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {}

    public function index(Request $request)
    {
        $summary = $this->cartService->getCartSummary();
    
        if ($request->wantsJson()) {
            return response()->json([
                'cartItems' => $summary['items'],   // 👈 match Alpine expectation
                'subtotal'  => $summary['total'],   // 👈 subtotal = total (since shipping free)
                'count'     => $summary['count'],
            ]);
        }
    
        // Blade still works fine
        return view('frontend.cart.index', [
            'cartItems' => $summary['items'],
            'subtotal'  => $summary['total'],
        ]);
    }
    

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1|max:99',
        ]);
    
        $this->cartService->addToCart($request->product_id, $request->quantity);
    
        $summary = $this->cartService->getCartSummary();
    
        return response()->json([
            'message'    => 'Product added to cart successfully!',
            'cartItems'  => $summary['items'],   // 👈 return items too
            'subtotal'   => $summary['total'],
            'count'      => $summary['count'],
        ]);
    }    

    public function update(Request $request, int $id)
    {
        $request->validate(['quantity' => 'required|integer|min:1|max:99']);
        $this->cartService->updateCartItem($id, $request->quantity);

        return response()->json(['message' => 'Cart updated successfully']);
    }

    public function destroy(int $id)
    {
        $this->cartService->removeCartItem($id);

        return response()->json(['message' => 'Item removed successfully!']);
    }

    public function clearAll()
    {
        $this->cartService->clearCart();

        return response()->json(['message' => 'Cart cleared successfully']);
    }

    public function count()
    {
        return response()->json([
            'count' => $this->cartService->getCartSummary()['count'],
        ]);
    }
}