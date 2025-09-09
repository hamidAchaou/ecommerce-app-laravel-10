<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Client;
use App\Repositories\Frontend\CartRepository;
use App\Repositories\OrderRepository;
use App\Services\Frontend\ClientService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Stripe\Webhook;
use Stripe\Checkout\Session as StripeSession;

class StripeWebhookController extends Controller
{
    protected CartRepository $cartRepository;
    protected OrderRepository $orderRepository;
    protected ClientService $clientService;

    public function __construct(
        CartRepository $cartRepository,
        OrderRepository $orderRepository,
        ClientService $clientService
    ) {
        $this->cartRepository = $cartRepository;
        $this->orderRepository = $orderRepository;
        $this->clientService  = $clientService;
    }

    /**
     * Handle Stripe webhook events
     */
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\UnexpectedValueException $e) {
            return response()->json(['error' => 'Invalid payload'], Response::HTTP_BAD_REQUEST);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], Response::HTTP_BAD_REQUEST);
        }

        switch ($event->type) {
            case 'checkout.session.completed':
                return $this->processCheckoutCompleted($event->data->object);

            default:
                return response()->json(['status' => 'ignored']);
        }
    }

    /**
     * Handle checkout.session.completed event
     */
    protected function processCheckoutCompleted(StripeSession $session)
    {
        Log::info('Processing checkout.session.completed', [
            'session_id'     => $session->id,
            'payment_status' => $session->payment_status,
            'customer_email' => $session->customer_email,
        ]);

        try {
            $sessionData = DB::table('stripe_sessions')
                ->where('session_id', $session->id)
                ->first();

            if (!$sessionData) {
                throw new \Exception("Stripe session not found in DB: {$session->id}");
            }

            $cartItems  = json_decode($sessionData->cart_items, true);
            $clientData = json_decode($sessionData->client_data, true);
            $user       = User::findOrFail($sessionData->user_id);

            // Ensure client exists
            $client = $user->client ?? $this->clientService->getOrCreateClient($user, $clientData);

            $total = $session->amount_total / 100;

            // Save payment
            $payment = Payment::create([
                'amount'         => $total,
                'method'         => 'stripe',
                'status'         => $session->payment_status === 'paid' ? 'completed' : 'pending',
                'transaction_id' => $session->payment_intent,
                'metadata'       => [
                    'stripe_session_id' => $session->id,
                    'customer_email'    => $session->customer_email,
                ],
            ]);

            // Save order
            $order = $this->orderRepository->create([
                'client_id'        => $client->id,
                'payment_id'       => $payment->id,
                'total_amount'     => $total,
                'status'           => $session->payment_status === 'paid' ? 'completed' : 'pending',
                'stripe_session_id'=> $session->id,
            ]);

            // Save order items
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $item['id'],
                    'quantity'   => $item['quantity'],
                    'price'      => $item['price'],
                ]);
            }

            // ✅ Clear cart via repository (client_id, not user_id)
            $this->cartRepository->clear($client->id);

            // Clean up stripe_sessions row
            DB::table('stripe_sessions')->where('session_id', $session->id)->delete();

            Log::info('Order created successfully from webhook', [
                'order_id'   => $order->id,
                'payment_id' => $payment->id,
                'client_id'  => $client->id,
                'total'      => $total,
            ]);

            return response()->json([
                'status'     => 'success',
                'order_id'   => $order->id,
                'payment_id' => $payment->id,
            ]);

        } catch (\Throwable $e) {
            Log::error('Error handling checkout.session.completed', [
                'session_id' => $session->id,
                'error'      => $e->getMessage(),
            ]);
            return response()->json(['error' => 'Processing failed'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}