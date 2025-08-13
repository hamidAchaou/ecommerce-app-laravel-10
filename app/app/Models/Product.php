<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    // Only set keyType to 'string' if your IDs are actually strings (UUIDs, etc.)
    // If they're auto-incrementing integers, remove this line or set to 'int'
    protected $keyType = 'int'; // Change this from 'string' to 'int' if using integer IDs
    
    protected $fillable = [
        'title',
        'description',
        'price',
        'stock',
        'category_id',
    ];

    /**
     * Get the category of the product.
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'id');
    }
    /**
     * Get the cart items for this product.
     */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'product_id', 'id');
    }

    /**
     * Get the order items for this product.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_id', 'id');
    }
}