<?php

namespace Rennokki\Cart\Traits;

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
        return (bool) ! is_null($this->carts()->find($cartId));
    }

    public function createCart($name)
    {
        $cartModel = config('cart.models.cart');

        return $this->carts()->save(new $cartModel([
            'name' => $name,
        ]));
    }

    public function deleteCart($cartId)
    {
        if (! $this->hasCart($cartId)) {
            return false;
        }

        $cart = $this->getCart($cartId);

        return (bool) ((($cart->isEmpty()) ? true : $cart->products()->delete()) && $cart->delete());
    }

    public function getCart($cartId)
    {
        return $this->carts()->find($cartId);
    }
}
