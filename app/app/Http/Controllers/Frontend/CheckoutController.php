<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Repositories\Admin\ProductRepository;
use App\Traits\CartManagement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class CheckoutController extends Controller
{
    use CartManagement;

    protected ProductRepository $productRepo;

    /**
     * CheckoutController constructor.
     *
     * @param ProductRepository $productRepo
     */
    public function __construct(ProductRepository $productRepo)
    {
        $this->productRepo = $productRepo;
    }

    /**
     * Display the checkout page.
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        $cartItems = $this->getCartItems($request);

        if ($cartItems->isEmpty()) {
            return redirect()
                ->route('products.index')
                ->with('error', 'Your cart is empty. Please add some items before checkout.');
        }

        $total = $this->calculateTotal($cartItems);

        Log::info('Checkout page accessed', [
            'user_id' => Auth::id(),
            'cart_items_count' => $cartItems->count(),
            'total' => $total,
        ]);

        return view('frontend.checkout.index', compact('cartItems', 'total'));
    }

    /**
     * Show the payment page after checkout form.
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function payment(Request $request)
    {
        $cartItems = $this->getCartItems($request);

        if ($cartItems->isEmpty()) {
            return redirect()
                ->route('products.index')
                ->with('error', 'Your cart is empty. Please add some items before checkout.');
        }

        $subtotal = $this->calculateTotal($cartItems);
        $cartItemsArray = $cartItems->toArray();

        Log::info('Payment page accessed', [
            'user_id' => Auth::id(),
            'session_id' => $request->session()->getId(),
            'cart_items_count' => $cartItems->count(),
        ]);

        return view('frontend.checkout.payment', compact('cartItemsArray', 'cartItems', 'subtotal'));
    }

    /**
     * Handle payment processing (e.g., Stripe, PayPal).
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processPayment(Request $request)
    {
        $request->validate([
            'payment_method'   => 'required|string',
            'billing_address'  => 'required|string|max:255',
        ]);

        $cartItems = $this->getCartItems($request);

        if ($cartItems->isEmpty()) {
            return redirect()
                ->route('products.index')
                ->with('error', 'Your cart is empty.');
        }

        try {
            // TODO: Implement actual payment gateway integration

            Log::info('Payment processed successfully', [
                'user_id' => Auth::id(),
                'cart_items_count' => $cartItems->count(),
                'total' => $this->calculateTotal($cartItems),
            ]);

            $this->clearCart($request);

            return redirect()
                ->route('home')
                ->with('success', 'Payment successful! Thank you for your order.');
        } catch (\Exception $e) {
            Log::error('Payment processing failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Payment processing failed. Please try again.');
        }
    }

    /**
     * Retrieve cart items for both guest and authenticated users.
     *
     * @param Request $request
     * @return \Illuminate\Support\Collection
     */
    protected function getCartItems(Request $request)
    {
        if ($user = Auth::user()) {
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
                                'quantity'   => min($item['quantity'], 99),
                            ]);
                        }
                    }

                    $request->session()->forget('cart');
                } catch (\Exception $e) {
                    Log::error('Error merging session cart in checkout', [
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            $userCart = $user->cart()->first();
            if (!$userCart) {
                return collect([]);
            }

            $dbCartItems = $userCart->cartItems()->with('product.images')->get();

            return $dbCartItems->map(fn($item) => [
                'id'       => $item->product_id,
                'title'    => $item->product->title ?? 'Unknown Product',
                'price'    => (float) ($item->product->price ?? 0),
                'quantity' => (int) $item->quantity,
                'image'    => $item->product
                    ? $item->product->mainImageUrl()
                    : asset('images/placeholders/product-placeholder.png'),
                'product'  => $item->product,
            ]);
        }

        $sessionCart = collect($request->session()->get('cart', []));

        return $sessionCart->map(function ($item) {
            $product = \App\Models\Product::with('images')->find($item['id']);
            if (!$product) {
                return null;
            }

            return [
                'id'       => (int) $item['id'],
                'title'    => $product->title,
                'price'    => (float) $product->price,
                'quantity' => (int) $item['quantity'],
                'image'    => $product->mainImageUrl(),
                'product'  => $product,
            ];
        })->filter();
    }

    /**
     * Calculate the total amount of the cart.
     *
     * @param \Illuminate\Support\Collection $cartItems
     * @return float
     */
    protected function calculateTotal($cartItems): float
    {
        return $cartItems->sum(fn($item) => (float) $item['price'] * (int) $item['quantity']);
    }

    /**
     * Clear the cart after successful payment.
     *
     * @param Request $request
     * @return void
     */
    protected function clearCart(Request $request): void
    {
        if ($user = Auth::user()) {
            $cart = $user->cart()->first();
            if ($cart) {
                $cart->cartItems()->delete();
            }
        } else {
            $request->session()->forget('cart');
        }
    }

    /**
     * Get cart summary for API/AJAX.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCartSummaryApi(Request $request)
    {
        $summary = $this->getCartSummary($request);
        return response()->json($summary);
    }
}