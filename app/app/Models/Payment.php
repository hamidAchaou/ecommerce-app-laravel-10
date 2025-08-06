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
        'method',
        'status',
        'amount',
        'transaction_id',
    ];

    /**
     * Get the order associated with the payment.
     */
    public function order()
    {
        return $this->hasOne(Order::class, 'payment_id', 'id');
    }
}