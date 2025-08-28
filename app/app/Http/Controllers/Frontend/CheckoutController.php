<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\CheckoutRequest;
use App\Services\Frontend\CheckoutService;
use App\Services\Frontend\ClientService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        private CheckoutService $checkoutService,
        private ClientService $clientService
        ) {}
        

    /**
     * Show checkout page
     */
    public function index(): View
    {
        $cartData = $this->checkoutService->getCartData();
    
        // أفضل practice: eager load cities
        $countries = $this->clientService->getCountriesWithCities();
    
        return view('frontend.checkout.index', [
            'cartItems'  => $cartData['items'],
            'cartTotal'  => $cartData['total'],
            'countries'  => $countries,
        ]);
    }
    

    /**
     * Process checkout
     */
    public function process(CheckoutRequest $request): RedirectResponse
{
    try {
        $order = $this->checkoutService->placeOrder($request->validated());
        return redirect()->route('orders.show', $order->id)
                         ->with('success', 'Order placed successfully!');
    } catch (\Exception $e) {
        return redirect()->back()->withErrors(['checkout' => $e->getMessage()]);
    }
}

}