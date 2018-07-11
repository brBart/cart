<?php

namespace Rennokki\Cart\Models;

use Illuminate\Database\Eloquent\Model;

class CartModel extends Model
{
    protected $table = 'carts';
    protected $fillable = [
        'model_id', 'model_type', 'name',
    ];
    protected $dates = [
        //
    ];

    public function model()
    {
        return $this->morphTo();
    }

    public function products()
    {
        return $this->hasMany(config('cart.models.cartProduct'), 'cart_id');
    }

    public function isEmpty()
    {
        return (bool) ($this->products()->count() == 0);
    }

    public function hasProduct($sku)
    {
        return (bool) ($this->products()->where('sku', $sku)->count() == 1);
    }

    public function getProduct($sku)
    {
        return $this->products()->where('sku', $sku)->first();
    }

    public function getProducts()
    {
        if ($this->isEmpty()) {
            return [];
        }

        return $this->products()->get();
    }

    public function total()
    {
        if ($this->isEmpty()) {
            return (float) 0.00;
        }

        $subtotal = (float) 0.00;

        foreach ($this->products()->get() as $product) {
            $subtotal += (float) $product->total();
        }

        return (float) $subtotal;
    }

    public function addProduct($sku, $name, $unitPrice, $quantity, $details)
    {
        if ($this->hasProduct($sku)) {
            $product = $this->getProduct($sku);

            $product->update([
                'name' => ($product->name != $name) ? $name : $product->name,
                'unit_price' => ($product->unit_price != $unitPrice) ? $unitPrice : $product->unit_price,
                'quantity' => ($product->quantity != $quantity) ? ($product->quantity + $quantity) : $product->quantity,
                'details' => ($product->details != $details) ? $details : $product->details,
            ]);

            return $product;
        }

        $productModel = config('cart.models.cartProduct');

        return $this->products()->save(new $productModel([
            'sku' => $sku,
            'name' => $name,
            'unit_price' => $unitPrice,
            'quantity' => $quantity,
            'details' => $details,
        ]));
    }

    public function deleteProduct($sku)
    {
        if (! $this->hasProduct($sku)) {
            return false;
        }

        $product = $this->getProduct($sku);

        return $product->delete();
    }

    public function updateSkuFor($sku, $newSku)
    {
        if (! $this->hasProduct($sku) || $this->hasProduct($newSku)) {
            return false;
        }

        $product = $this->getProduct($sku);

        $product->update([
            'sku' => $newSku,
        ]);

        return $product;
    }

    public function updateNameFor($sku, $newName)
    {
        if (! $this->hasProduct($sku)) {
            return false;
        }

        $product = $this->getProduct($sku);

        $product->update([
            'name' =>  $newName,
        ]);

        return $product;
    }

    public function updateUnitPriceFor($sku, $newUnitPrice)
    {
        if (! $this->hasProduct($sku)) {
            return false;
        }

        $product = $this->getProduct($sku);

        $product->update([
            'unit_price' => $newUnitPrice,
        ]);

        return $product;
    }

    public function updateDetailsFor($sku, $newDetails)
    {
        if (! $this->hasProduct($sku)) {
            return false;
        }

        $product = $this->getProduct($sku);

        $product->update([
            'details' => $newDetails,
        ]);

        return $product;
    }

    public function updateQuantityFor($sku, $newQuantity)
    {
        if (! $this->hasProduct($sku)) {
            return false;
        }

        $product = $this->getProduct($sku);

        $product->update([
            'quantity' => $newQuantity,
        ]);

        return $product;
    }
}
