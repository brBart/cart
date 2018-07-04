<?php

namespace Rennokki\Cart\Traits;

use Carbon\Carbon;

trait HasCarts
{
    /**
     * Get Carts relatinship.
     *
     * @return morphMany Relatinship.
     */
    public function carts()
    {
        return $this->morphMany(config('cart.models.cart'), 'model');
    }

    public function hasCart($cartId)
    {
        return (bool) !is_null($this->carts()->find($cartId));
    }

    public function cartHasProduct($cartId, $sku)
    {
        if(!$this->hasCart($cartId)) {
            return false;
        }

        $cart = $this->getCart($cartId);

        return (bool) ($cart->products()->where('sku', $sku)->count() == 1);
    }

    public function createCart($name)
    {
        $cartModel = config('cart.models.cart');

        return $this->carts()->save(new $cartModel([
            'name' => $name,
        ]));
    }

    public function getCart($cartId)
    {
        return $this->carts()->find($cartId);
    }

    public function getCartProducts($cartId)
    {
        if(!$this->hasCart($cartId)) {
            return null;
        }

        $cart = $this->getCart($cartId);

        return $cart->products()->get();
    }

    public function getCartProduct($cartId, $sku)
    {
        if(!$this->cartHasProduct($cartId, $sku)) {
            return null;
        }

        return $this->getCart($cartId)->products()->where('sku', $sku)->first();
    }

    public function addProductTo($cartId, $sku, $name, $unitPrice, $quantity, $attributes)
    {
        if(!$this->hasCart($cartId)) {
            return false;
        }

        $cart = $this->getCart($cartId);

        return $cart->addProduct($sku, $name, $unitPrice, $quantity, $attributes);
    }

    public function deleteProductFrom($cartId, $sku)
    {
        if(!$this->hasCart($cartId)) {
            return false;
        }

        $cart = $this->getCart($cartId);

        return $cart->deleteProduct($sku);
    }

    public function updateProductSkuFor($cartId, $sku, $newSku)
    {
        if(!$this->hasCart($cartId)) {
            return false;
        }

        $cart = $this->getCart($cartId);

        return $cart->updateSkuFor($sku, $newSku);
    }

    public function updateProductNameFor($cartId, $sku, $newName)
    {
        if(!$this->hasCart($cartId)) {
            return false;
        }

        $cart = $this->getCart($cartId);

        return $cart->updateNameFor($sku, $newName);
    }

    public function updateProductUnitPriceFor($cartId, $sku, $newUnitPrice)
    {
        if(!$this->hasCart($cartId)) {
            return false;
        }

        $cart = $this->getCart($cartId);

        return $cart->updateUnitPriceFor($sku, $newUnitPrice);
    }

    public function updateProductAttributesFor($cartId, $sku, $newAttributes)
    {
        if(!$this->hasCart($cartId)) {
            return false;
        }

        $cart = $this->getCart($cartId);

        return $cart->updateAttributesFor($sku, $newAttributes);
    }

    public function updateProductQuantityFor($cartId, $sku, $newQuantity)
    {
        if(!$this->hasCart($cartId)) {
            return false;
        }

        $cart = $this->getCart($cartId);

        return $cart->upgradeQuantityFor($sku, $newQuantity);
    }

    public function addProductQuantityFor($cartId, $sku, $amount)
    {
        if(!$this->hasCart($cartId)) {
            return false;
        }

        $cart = $this->getCart($cartId);

        return $cart->addQuantityFor($sku, $amount);
    }

    public function subtractProductQuantityFor($cartId, $sku, $amount)
    {
        if(!$this->hasCart($cartId)) {
            return false;
        }

        $cart = $this->getCart($cartId);

        return $cart->subtractQuantityFor($sku, $amount);
    }

}
