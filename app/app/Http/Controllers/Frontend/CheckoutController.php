<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\CheckoutRequest;
use App\Services\Frontend\CheckoutService;
use App\Services\Frontend\ClientService;
use App\Services\Frontend\CartService;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class CheckoutController extends Controller
{
    public function __construct(
        private CheckoutService $checkoutService,
        private ClientService $clientService,
        private CartService $cartService,
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

    /** Stripe checkout endpoint */
    public function stripeCheckout(CheckoutRequest $request): JsonResponse
    {   
        try {
            $validated = $request->validated();

            $user = auth()->user();
            Log::info('Stripe checkout payload', [
                'validated' => $validated,
                'session_id' => $session->id ?? null
            ]);
                   
            $client = $this->clientService->getOrCreateClient($user, $validated);
    
            $session = $this->checkoutService->createStripeSession([
                ...$validated,
                'client_id' => $client->id,
                'user_id'   => $user->id,
            ]);
            // // dd($session);
            // dd([
            //     'validated' => $validated,
            //     'session_id' => $session->id ?? null
            // ]);
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

    /** Create Stripe session */
    private function createStripeSession(array $clientData, $user, $client, $cartItems)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        // Prepare line items for Stripe
        $lineItems = $cartItems->map(function ($item) {
            return [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $item['title'],
                        'description' => $item['description'] ?? null
                    ],
                    'unit_amount' => intval($item['price'] * 100), // Convert to cents
                ],
                'quantity' => intval($item['quantity'])
            ];
        })->toArray();

        // Prepare metadata
        $metadata = [
            'user_id' => $user->id,
            'client_id' => $client->id,
            'cart_items' => json_encode($cartItems->toArray()),
            'client_data' => json_encode($clientData)
        ];

        // Create Stripe session
        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('checkout.index'),
            'customer_email' => $user->email,
            'metadata' => $metadata,
        ]);

        Log::info('Stripe session created successfully', [
            'session_id' => $session->id,
            'user_id' => $user->id,
            'items_count' => $cartItems->count(),
            'total_amount' => $cartItems->sum(fn($item) => $item['price'] * $item['quantity'])
        ]);

        return $session;
    }

    public function success(Request $request): View
    {
        $user = auth()->user();
        if (!$user) {
            return view('frontend.checkout.error', [
                'message' => 'You must be logged in to view orders',
            ]);
        }
    
        // Retrieve latest order for this client
        $order = $this->checkoutService->getLatestOrderForClient($user->client->id);
    
        if (!$order) {
            Log::warning('Order not found for client', [
                'client_id' => $user->client->id,
            ]);
    
            return view('frontend.checkout.success', [
                'order' => null,
                'processing' => true,
            ]);
        }
    
        return view('frontend.checkout.success', [
            'order' => $order,
            'processing' => false,
        ]);
    }
    
}