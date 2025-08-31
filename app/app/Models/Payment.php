<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'stripe_session_id',
        'stripe_payment_intent_id',
        'amount',
        'currency',
        'status',
        'payment_method',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /**
     * Get the order for this payment.
     */
    public function order()
    {
        return $this->hasOne(Order::class, 'payment_id', 'id');
    }
}