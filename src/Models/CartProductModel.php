<?php

namespace Rennokki\Cart\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class CartProductModel extends Model
{
    protected $table = 'carts_products';
    protected $fillable = [
        'cart_id', 'sku', 'name', 'unit_price', 'quantity',
        'attributes',
    ];
    protected $casts = [
        'attributes' => 'array',
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

    public function totalPrice()
    {
        return (float) ($this->quantity * $this->unit_price);
    }
}
