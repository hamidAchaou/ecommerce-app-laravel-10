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

class CheckoutController extends Controller
{
    public function __construct(
        private CheckoutService $checkoutService,
        private ClientService $clientService
    ) {}

    /** Show checkout page */
    public function index(): View
    {
        $user = auth()->user();
        $client = $user->client ?? null;
        $cartData = $this->checkoutService->getCartData();
        $countries = $this->clientService->getCountriesWithCities();

        return view('frontend.checkout.index', [
            'client' => $client,
            'cartItems' => $cartData['items'],
            'cartTotal' => $cartData['total'],
            'countries' => $countries,
        ]);
    }

    public function stripeCheckout(CheckoutRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $user = auth()->user();
            
            $client = $this->clientService->getOrCreateClient($user, $validated);
    
            $session = $this->checkoutService->createStripeSession([
                ...$validated,
                'client_id' => $client->id,
                'user_id'   => $user->id,
            ]);
            // dd($session);
    
            return response()->json(['id' => $session->id]);
        } catch (\Exception $e) {
            Log::error('Stripe checkout error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
            ]);
    
            $status = $e instanceof \Symfony\Component\HttpKernel\Exception\HttpException
                ? $e->getStatusCode()
                : 500;
    
            return response()->json(['error' => $e->getMessage()], $status);
        }
    }


    /** Show success page after payment */
    public function success(Request $request): View
    {
        $order = $this->checkoutService->getOrderFromStripeSession($request->query('session_id'));

        return view('frontend.checkout.success', [
            'order' => $order,
            'sessionId' => $request->query('session_id')
        ]);
    }
}