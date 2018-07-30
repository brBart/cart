<?php

namespace Rennokki\Cart\Traits;

trait HasCarts
{
    /**
     * Get Carts relatinship.
     *
     * @return morphMany Relationship.
     */
    public function carts()
    {
        return $this->morphMany(config('cart.models.cart'), 'model');
    }

    /**
     * Check if has a cart with a certain id.
     *
     * @param string $cartId The cart id.
     * @return bool
     */
    public function hasCart(string $cartId): bool
    {
        return (bool) ! is_null($this->carts()->find($cartId));
    }

    /**
     * Create a new cart.
     *
     * @param string $name The cart name.
     * @return CartModel The cart model instance.
     */
    public function createCart(string $name)
    {
        $cartModel = config('cart.models.cart');

        return $this->carts()->save(new $cartModel([
            'name' => $name,
        ]));
    }

    /**
     * Delete a cart based on ID.
     *
     * @param string $cartId The cart ID.
     * @return bool
     */
    public function deleteCart(string $cartId): bool
    {
        if (! $this->hasCart($cartId)) {
            return false;
        }

        $cart = $this->getCart($cartId);

        return (bool) ((($cart->isEmpty()) ? true : $cart->products()->delete()) && $cart->delete());
    }

    /**
     * Get a cart based on ID.
     *
     * @param string $cartId The cart Id.
     * @return CartModel The cart model instance.
     */
    public function getCart(string $cartId)
    {
        return $this->carts()->find($cartId);
    }
}
