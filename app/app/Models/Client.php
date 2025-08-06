<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'user_id',
        'phone',
        'address',
        'city_id',
        'country_id',
    ];

    /**
     * Get the user that owns the client.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get the city of the client.
     */
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id', 'id');
    }

    /**
     * Get the country of the client.
     */
    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    /**
     * Get the cart associated with the client.
     */
    public function cart()
    {
        return $this->hasOne(Cart::class, 'client_id', 'id');
    }

    /**
     * Get the orders placed by the client.
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'client_id', 'id');
    }
}