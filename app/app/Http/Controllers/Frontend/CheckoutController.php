<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\CheckoutRequest;
use App\Services\Frontend\CheckoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(private CheckoutService $checkoutService) {}

    /**
     * Show checkout page
     */
    public function index(): View
    {
        $cartData = $this->checkoutService->getCartData();

        return view('frontend.checkout.index', [
            'cartItems' => $cartData['items'],
            'cartTotal' => $cartData['total'],
        ]);
    }

    /**
     * Process checkout
     */
    public function process(CheckoutRequest $request): RedirectResponse
    {
        try {
            $order = $this->checkoutService->processCheckout($request->validated());
            return redirect()->route('orders.show', $order->id)
                             ->with('success', 'Order placed successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['checkout' => $e->getMessage()]);
        }
    }
}