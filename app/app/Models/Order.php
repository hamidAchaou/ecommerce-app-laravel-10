<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'client_id',
        'status',
        'total_amount',
        'payment_id',
        'shipping_name',
        'shipping_phone',
        'shipping_address',
        'country_id',
        'city_id',
        'notes',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
    ];

    /**
     * Get the client/user that placed the order.
     */
    public function client()
    {
        return $this->belongsTo(\App\Models\User::class, 'client_id', 'id');
    }

    /**
     * Get the user that placed the order (alias for client).
     */
    public function user()
    {
        return $this->client();
    }

    /**
     * Get the payment for the order.
     */
    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id', 'id');
    }

    /**
     * Get the items in the order.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }

    /**
     * Get the country for shipping.
     */
    public function country()
    {
        return $this->belongsTo(\App\Models\Country::class, 'country_id', 'id');
    }

    /**
     * Get the city for shipping.
     */
    public function city()
    {
        return $this->belongsTo(\App\Models\City::class, 'city_id', 'id');
    }
}