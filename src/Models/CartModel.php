<?php

namespace Rennokki\Cart\Models;

use Illuminate\Database\Eloquent\Model;

class CartModel extends Model
{
    protected $table = 'carts';
    protected $fillable = [
        'model_id', 'model_type', 'name',
        'coupon_code',
    ];
    protected $dates = [
        //
    ];

    public function model()
    {
        return $this->morphTo();
    }

    /**
     * Get cart products.
     *
     * @return hasMany Relationship with cart products.
     */
    public function products()
    {
        return $this->hasMany(config('cart.models.cartProduct'), 'cart_id');
    }

    /**
     * Check wether the cart is empty or not.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return (bool) ($this->products()->count() == 0);
    }

    /**
     * Check if the cart has a coupon attached.
     *
     * @return bool
     */
    public function hasCoupon()
    {
        return (bool) ! is_null($this->coupon_code);
    }

    /**
     * Attach a coupon code to the cart.
     *
     * @param string $couponCode The coupon code.
     * @return bool
     */
    public function setCoupon($couponCode)
    {
        return $this->update([
            'coupon_code' => $couponCode,
        ]);
    }

    /**
     * Update the cart coupon.
     *
     * @param string $couponCode The coupon code.
     * @return bool
     */
    public function updateCoupon($couponCode)
    {
        return $this->setCoupon($couponCode);
    }

    /**
     * Delete the coupon attached to the cart.
     *
     * @return bool
     */
    public function deleteCoupon()
    {
        return $this->update([
            'coupon_code' => null,
        ]);
    }

    /**
     * Check if the cart has a product with a certain SKU.
     *
     * @param string $sku The product SKU.
     * @return bool
     */
    public function hasProduct($sku)
    {
        return (bool) ($this->products()->where('sku', $sku)->count() == 1);
    }

    /**
     * Get the product based on SKU.
     *
     * @param string $sku The product SKU.
     * @return null|CartProduct The cart product.
     */
    public function getProduct($sku)
    {
        return $this->products()->sku($sku)->first();
    }

    /**
     * Get the cart products.
     *
     * @return null|CartProduct The cart products.
     */
    public function getProducts()
    {
        if ($this->isEmpty()) {
            return [];
        }

        return $this->products()->get();
    }

    /**
     * Get the total price for the cart.
     *
     * @return float $subtotal The cart total
     */
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

    /**
     * Add a product to the cart.
     *
     * @param string $sku The product SKU.
     * @param string $name The product name.
     * @param float $unitPrice The unit price.
     * @param int $quantity The quantity.
     * @param array $details The additional details for the product.
     * @return bool
     */
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

    /**
     * Delete a product from the cart.
     *
     * @param string $sku The product SKU.
     * @return bool
     */
    public function deleteProduct($sku)
    {
        if (! $this->hasProduct($sku)) {
            return false;
        }

        $product = $this->getProduct($sku);

        return $product->delete();
    }

    /**
     * Update SKU for a cart product.
     *
     * @param string $sku The product SKU.
     * @param string $newSku The new product SKU.
     * @return bool
     */
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

    /**
     * Update name for a cart product.
     *
     * @param string $sku The product SKU.
     * @param string $newName The new product name.
     * @return bool
     */
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

    /**
     * Update unit price for a cart product.
     *
     * @param string $sku The product SKU.
     * @param string $newUnitPrice The new unit price.
     * @return bool
     */
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

    /**
     * Update additional details for a cart product.
     *
     * @param string $sku The product SKU.
     * @param array $newDetails The new details array.
     * @return bool
     */
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

    /**
     * Update the quantity for a cart product.
     *
     * @param string $sku The product SKU.
     * @param int $newQuantity The new product quantity.
     * @return bool
     */
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
