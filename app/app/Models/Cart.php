<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $primaryKey = 'id';
    // public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'client_id',
        'created_at',
    ];

    /**
     * Get the client that owns the cart.
     */
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }

    /**
     * Get the items in the cart.
     */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'cart_id', 'id');
    }
}