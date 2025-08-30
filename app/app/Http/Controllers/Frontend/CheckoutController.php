<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\CheckoutRequest;
use App\Services\Frontend\CheckoutService;
use App\Services\Frontend\ClientService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

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
            Log::error('Checkout process error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->withErrors(['checkout' => $e->getMessage()]);
        }
    }

    public function stripeCheckout(Request $request): JsonResponse
    {
        try {
            // Manual validation instead of CheckoutRequest to avoid redirect
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
                'address' => 'required|string|max:500',
                'country_id' => 'required|exists:countries,id',
                'city_id' => 'required|exists:cities,id',
                'notes' => 'nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Validation failed: ' . $validator->errors()->first()
                ], 422);
            }

            // Check if user is authenticated
            if (!auth()->check()) {
                return response()->json(['error' => 'Authentication required'], 401);
            }

            // Check Stripe configuration
            if (!config('stripe.key') || !config('stripe.secret')) {
                Log::error('Stripe configuration missing');
                return response()->json(['error' => 'Payment system not configured'], 500);
            }

            $session = $this->checkoutService->createStripeSession($validator->validated());
            
            return response()->json(['id' => $session->id]);
            
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            Log::error('Stripe Invalid Request:', [
                'message' => $e->getMessage(),
                'type' => get_class($e)
            ]);
            return response()->json(['error' => 'Invalid payment request: ' . $e->getMessage()], 400);
            
        } catch (\Stripe\Exception\AuthenticationException $e) {
            Log::error('Stripe Authentication Error:', [
                'message' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Payment system authentication failed'], 500);
            
        } catch (\Exception $e) {
            Log::error('Stripe checkout error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'error' => 'Payment processing failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show success page after payment
     */
    public function success(): View
    {
        return view('frontend.checkout.success');
    }
}