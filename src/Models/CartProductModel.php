<?php

namespace Rennokki\Cart\Models;

use Illuminate\Database\Eloquent\Model;

class CartProductModel extends Model
{
    protected $table = 'carts_products';
    protected $fillable = [
        'cart_id', 'sku', 'name', 'unit_price', 'quantity',
        'details',
    ];
    protected $casts = [
        'details' => 'object',
        'unit_price' => 'float',
    ];
    protected $dates = [
        //
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

    public function total()
    {
        return (float) ($this->quantity * $this->unit_price);
    }
}
