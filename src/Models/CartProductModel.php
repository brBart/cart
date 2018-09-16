<?php

namespace Rennokki\Cart\Models;

use Illuminate\Database\Eloquent\Model;

class CartProductModel extends Model
{
    protected $table = 'carts_products';
    protected $guarded = [];
    protected $casts = [
        'details' => 'object',
        'quantity' => 'float',
        'unit_price' => 'float',
    ];

    public function model()
    {
        return $this->morphTo();
    }

    public function cart()
    {
        return $this->belongsTo(config('cart.models.cart'), 'cart_id');
    }

    public function scopeSku($query, $sku)
    {
        return $query->where('sku', $sku);
    }

    public function total(): float
    {
        return (float) ($this->quantity * $this->unit_price);
    }
}
