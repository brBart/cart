<?php

namespace Rennokki\Cart\Models;

use Carbon\Carbon;
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

    public function addProduct($sku, $name, $unitPrice, $quantity, $attributes)
    {
        if($this->hasProduct($sku)) {
            $product = $this->getProduct($sku);

            $product->update([
                'name' => ($product->name != $name) ? $name : $product->name,
                'unit_price' => ($product->unit_price != $unitPrice) ? $unitPrice : $product->unit_price,
                'quantity' => ($product->quantity != $quantity) ? ($product->quantity + $quantity) : $product->quantity,
                'attributes' => ($product->attributes != $attributes) ? $attributes : $product->attributes,
            ]);

            return $product;
        }

        $productModel = config('cart.models.cartProduct');

        return $this->products()->save(new $productModel([
            'sku' => $sku,
            'name' => $name,
            'unit_price' => $unitPrice,
            'quantity' => $quantity,
            'attributes' => $attributes,
        ]));
    }

    public function deleteProduct($sku)
    {
        if(!$this->hasProduct($sku)) {
            return false;
        }

        $product = $this->getProduct($sku);

        return $product->delete();
    }

    public function updateSkuFor($sku, $newSku)
    {
        if(!$this->hasProduct($sku)) {
            return false;
        }

        $product = $this->getProduct($sku);

        $product->update([
            'sku' => $sku,
        ]);

        return $product;
    }

    public function updateNameFor($sku, $newName)
    {
        if(!$this->hasProduct($sku)) {
            return false;
        }

        $product = $this->getProduct($sku);

        return $product->update([
            'name' =>  $newName,
        ]);
    }

    public function updateUnitPriceFor($sku, $newUnitPrice)
    {
        if(!$this->hasProduct($sku)) {
            return false;
        }

        $product = $this->getProduct($sku);

        return $product->update([
            'unit_price' => $newUnitPrice,
        ]);
    }

    public function updateAttributesFor($sku, $newAttributes)
    {
        if(!$this->hasProduct($sku)) {
            return false;
        }

        $product = $this->getProduct($sku);

        return $product->update([
            'attributes' => $newAttributes,
        ]);
    }

    public function updateQuantityFor($sku, $newQuantity)
    {
        if(!$this->hasProduct($sku)) {
            return false;
        }

        $product = $this->getProduct($sku);

        return $product->update([
            'quantity' => $newQuantity,
        ]);
    }

    public function addQuantityFor($sku, $amount)
    {
        if(!$this->hasProduct($sku)) {
            return false;
        }

        $product = $this->getProduct($sku);

        return $product->increment('quantity', $amount);
    }

    public function subtractQuantityFor($sku, $amount)
    {
        if(!$this->hasProduct($sku)) {
            return false;
        }

        $product = $this->getProduct($sku);

        return $product->decrement('quantity', $amount);
    }
}
