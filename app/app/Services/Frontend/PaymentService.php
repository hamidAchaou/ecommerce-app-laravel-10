<?php

namespace App\Services\Frontend;

use App\Models\Payment;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    /**
     * Create a payment record
     *
     * @param array $data
     * @return Payment
     */
    public function createPayment(array $data): Payment
    {
        $payment = Payment::create([
            'amount' => $data['amount'],
            'method' => $data['method'] ?? 'pending',
            'status' => $data['status'] ?? 'pending',
            'transaction_id' => $data['transaction_id'] ?? null,
            'metadata' => $data['metadata'] ?? null,
        ]);

        Log::info('Payment created', [
            'payment_id' => $payment->id,
            'amount' => $payment->amount,
            'method' => $payment->method,
            'status' => $payment->status
        ]);

        return $payment;
    }

    /**
     * Update payment status
     *
     * @param Payment $payment
     * @param string $status
     * @return Payment
     */
    public function updatePaymentStatus(Payment $payment, string $status): Payment
    {
        $validStatuses = ['pending', 'completed', 'failed', 'refunded'];
        
        if (!in_array($status, $validStatuses)) {
            throw new \Exception("Invalid payment status: {$status}");
        }

        $payment->update(['status' => $status]);

        Log::info('Payment status updated', [
            'payment_id' => $payment->id,
            'old_status' => $payment->getOriginal('status'),
            'new_status' => $status
        ]);

        return $payment->fresh();
    }

    /**
     * Get payment by transaction ID
     */
    public function getPaymentByTransaction(string $transactionId): ?Payment
    {
        return Payment::where('transaction_id', $transactionId)->first();
    }

    /**
     * Process refund (placeholder for future implementation)
     */
    public function processRefund(Payment $payment, float $amount = null): bool
    {
        // Implementation depends on payment gateway
        // For now, just update status
        $this->updatePaymentStatus($payment, 'refunded');
        
        return true;
    }
}