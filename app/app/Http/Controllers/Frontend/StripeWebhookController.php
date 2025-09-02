<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\Frontend\OrderService;
use App\Services\Frontend\PaymentService;
use Stripe\Stripe;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function handleWebhook(Request $request, OrderService $orderService, PaymentService $paymentService)
    {
        // Log everything for debugging
        Log::info('=== STRIPE WEBHOOK RECEIVED ===', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'headers' => $request->headers->all(),
            'payload_length' => strlen($request->getContent()),
            'raw_payload' => $request->getContent()
        ]);

        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = config('stripe.webhook_secret');

        // For debugging - temporarily disable signature verification
        // REMOVE THIS IN PRODUCTION!
        if (app()->environment('local')) {
            Log::warning('WEBHOOK SIGNATURE VERIFICATION DISABLED FOR LOCAL TESTING');
            try {
                $event = json_decode($payload, true, 512, JSON_THROW_ON_ERROR);
                Log::info('Event decoded successfully', ['event_type' => $event['type']]);
            } catch (\Exception $e) {
                Log::error('Failed to decode webhook payload', ['error' => $e->getMessage()]);
                return response()->json(['error' => 'Invalid payload'], 400);
            }
        } else {
            // Production signature verification
            if (empty($secret)) {
                Log::error('Stripe webhook secret not configured');
                return response()->json(['error' => 'Webhook secret not configured'], 500);
            }

            try {
                $event = Webhook::constructEvent($payload, $sigHeader, $secret);
                Log::info('Webhook signature verified successfully');
            } catch (\Exception $e) {
                Log::error('Stripe webhook signature verification failed', ['error' => $e->getMessage()]);
                return response()->json(['error' => 'Invalid signature'], 400);
            }
        }

        Log::info('Processing Stripe webhook event', [
            'event_type' => $event['type'] ?? 'unknown',
            'event_id' => $event['id'] ?? 'unknown',
            'event_data' => $event['data'] ?? []
        ]);

        try {
            switch ($event['type']) {
                case 'checkout.session.completed':
                    return $this->handleCheckoutSessionCompleted($event['data']['object'], $orderService);
                
                case 'payment_intent.succeeded':
                    Log::info('Payment intent succeeded - but we handle this in checkout.session.completed');
                    return response()->json(['status' => 'acknowledged']);
                
                default:
                    Log::info('Unhandled webhook event type', ['type' => $event['type']]);
                    return response()->json(['status' => 'ignored']);
            }
        } catch (\Exception $e) {
            Log::error('Webhook processing failed', [
                'event_type' => $event['type'] ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 200);
        }
    }

    /**
     * Handle checkout session completed event
     */
    private function handleCheckoutSessionCompleted(array $session, OrderService $orderService)
    {
        $sessionId = $session['id'];
        $paymentIntentId = $session['payment_intent'] ?? null;
        $userId = $session['metadata']['user_id'] ?? null;
        $sessionKey = $session['metadata']['session_key'] ?? null;

        Log::info('=== PROCESSING CHECKOUT SESSION COMPLETED ===', [
            'session_id' => $sessionId,
            'payment_intent' => $paymentIntentId,
            'user_id' => $userId,
            'session_key' => $sessionKey,
            'full_session_data' => $session
        ]);

        // Validate required data
        if (!$userId) {
            Log::error('User ID missing in session metadata');
            return response()->json(['error' => 'User ID missing'], 400);
        }

        if (!$sessionKey) {
            Log::error('Session key missing in session metadata');
            return response()->json(['error' => 'Session key missing'], 400);
        }

        // Check if order already exists
        if ($paymentIntentId) {
            $existingOrder = \App\Models\Order::whereHas('payment', function($query) use ($paymentIntentId) {
                $query->where('transaction_id', $paymentIntentId);
            })->first();
            
            if ($existingOrder) {
                Log::info('Order already exists for this payment', [
                    'order_id' => $existingOrder->id,
                    'payment_intent' => $paymentIntentId
                ]);
                return response()->json(['status' => 'already_processed', 'order_id' => $existingOrder->id]);
            }
        }

        try {
            // Create the order
            $order = $orderService->createOrderFromStripeSession((object)$session);

            Log::info('=== ORDER CREATED SUCCESSFULLY ===', [
                'session_id' => $sessionId,
                'order_id' => $order->id,
                'client_id' => $order->client_id,
                'payment_id' => $order->payment_id,
                'total_amount' => $order->total_amount
            ]);

            return response()->json([
                'status' => 'success', 
                'order_id' => $order->id,
                'client_id' => $order->client_id,
                'payment_id' => $order->payment_id
            ]);

        } catch (\Exception $e) {
            Log::error('=== ORDER CREATION FAILED ===', [
                'session_id' => $sessionId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => $userId,
                'session_key' => $sessionKey
            ]);
            
            throw $e;
        }
    }
}