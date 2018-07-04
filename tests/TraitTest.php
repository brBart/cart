<?php

namespace Rennokki\Cart\Test;

class CartTest extends TestCase
{
    protected $user;

    public function setUp()
    {
        parent::setUp();

        $this->user = factory(\Rennokki\Cart\Test\Models\User::class)->create();
    }

    public function testWithNoCarts()
    {
        $this->assertNull($this->user->carts()->first());
        $this->assertFalse($this->user->hasCart(0));
        $this->assertFalse($this->user->cartHasProduct(0, 0));
        $this->assertNull($this->user->getCart(0));
        $this->assertNull($this->user->getCartProducts(0));
        $this->assertNull($this->user->getCartProduct(0, 0));
    }

    public function testCreateCart()
    {
        $this->assertNotNull($cart1 = $this->user->createCart('My First Cart'));
        $this->assertNotNull($cart2 = $this->user->createCart('My Second Cart'));
        $this->assertNotNull($cart3 = $this->user->createCart('My Third Cart'));

        $this->assertEquals($this->user->carts()->count(), 3);
        $this->assertTrue($this->user->hasCart($cart1->id));
        $this->assertFalse($this->user->cartHasProduct($cart1->id, 0));
        $this->assertNotNull($this->user->getCart($cart1->id));
        $this->assertEquals(count($this->user->getCartProducts($cart1->id)), 0);
        $this->assertNull($this->user->getCartProduct($cart1->id, 0));
    }

    public function testAddProductsToCart()
    {
        $cart = $this->user->createCart('My First Cart');

        $this->assertFalse($this->user->addProductTo(0, 0, 'Skirt', 10.00, 1, ['material' => 'Cotton']));
        $this->assertNotNull($skirt = $this->user->addProductTo($cart->id, 0, 'Skirt', 10.00, 1, ['material' => 'Cotton']));

        $this->assertTrue($this->user->cartHasProduct($cart->id, $skirt->sku));
        $this->assertEquals(count($this->user->getCartProducts($cart->id)), 1);
        $this->assertNotNull($this->user->getCartProduct($cart->id, $skirt->sku));
    }

    public function testDeleteProductsFromCart()
    {
        $cart = $this->user->createCart('My First Cart');
        $skirt = $this->user->addProductTo($cart->id, 0, 'Skirt', 10.00, 1, ['material' => 'Cotton']);

        $this->assertFalse($this->user->deleteProductFrom($cart->id, 99));
        $this->assertTrue($this->user->deleteProductFrom(0, $skirt->sku));
        $this->assertTrue($this->user->deleteProductFrom($cart->id, $skirt->sku));

        $this->assertFalse($this->user->cartHasProduct($cart->id, $skirt->sku));
        $this->assertEquals(count($this->user->getCartProducts($cart->id)), 0);
        $this->assertNull($this->user->getCartProduct($cart->id, $skirt->sku));
    }

    public function testUpdateProductSku()
    {
        //
        $this->assertTrue(true);
    }

    public function testUpdateProductName()
    {
        //
        $this->assertTrue(true);
    }

    public function testUpdateProductUnitPrice()
    {
        //
        $this->assertTrue(true);
    }

    public function testUpdateProductAttributes()
    {
        //
        $this->assertTrue(true);
    }

    public function testUpdateProductQuantity()
    {
        //
        $this->assertTrue(true);
    }

    public function testAddProductQuantity()
    {
        //
        $this->assertTrue(true);
    }

    public function testSubtractProductQuantity()
    {
        //
        $this->assertTrue(true);
    }

    public function testTotalPrice()
    {
        //
        $this->assertTrue(true);
    }
}
