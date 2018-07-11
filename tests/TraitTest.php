<?php

namespace Rennokki\Cart\Test;

class TraitTest extends TestCase
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
        $this->assertNull($this->user->getCart(0));
    }

    public function testCreateCart()
    {
        $this->assertNotNull($cart1 = $this->user->createCart('My First Cart'));
        $this->assertNotNull($cart2 = $this->user->createCart('My Second Cart'));
        $this->assertNotNull($cart3 = $this->user->createCart('My Third Cart'));

        $this->assertEquals($this->user->carts()->count(), 3);
        $this->assertTrue($this->user->hasCart($cart1->id));
        $this->assertFalse($cart1->hasProduct(0));
        $this->assertNotNull($this->user->getCart($cart1->id));
        $this->assertEquals(count($cart1->getProducts($cart1->id)), 0);
        $this->assertNull($cart1->getProduct(0));
    }

    public function testDeleteEmptyCart()
    {
        $cart1 = $this->user->createCart('My First Cart');
        $cart2 = $this->user->createCart('My Second Cart');
        $cart3 = $this->user->createCart('My Third Cart');

        $this->assertFalse($this->user->deleteCart(99));
        $this->assertTrue($this->user->deleteCart($cart1->id));

        $this->assertEquals($this->user->carts()->count(), 2);

        $this->assertFalse($this->user->hasCart($cart1->id));
        $this->assertTrue($this->user->hasCart($cart2->id));
        $this->assertTrue($this->user->hasCart($cart3->id));
    }

    public function testDeleteCart()
    {
        $cart = $this->user->createCart('My First Cart');
        $skirt = $cart->addProduct(0, 'Skirt', 10.00, 1, ['material' => 'Cotton']);

        $this->assertFalse($this->user->deleteCart(99));
        $this->assertTrue($this->user->deleteCart($cart->id));

        $this->assertEquals($this->user->carts()->count(), 0);
        $this->assertFalse($this->user->hasCart($cart->id));
    }

    public function testAddProductsToCart()
    {
        $cart = $this->user->createCart('My First Cart');

        $this->assertNotNull($skirt = $cart->addProduct(0, 'Skirt', 10.00, 1, ['material' => 'Cotton']));

        $this->assertEquals($skirt->sku, 0);
        $this->assertEquals($skirt->name, 'Skirt');
        $this->assertEquals($skirt->unit_price, 10.00);
        $this->assertEquals($skirt->quantity, 1);
        $this->assertEquals($skirt->details->material, 'Cotton');

        $this->assertTrue($cart->hasProduct($skirt->sku));
        $this->assertEquals(count($cart->getProducts($cart->id)), 1);
        $this->assertNotNull($cart->getProduct($skirt->sku));
    }

    public function testDeleteProductsFromCart()
    {
        $cart = $this->user->createCart('My First Cart');
        $skirt = $cart->addProduct(0, 'Skirt', 10.00, 1, ['material' => 'Cotton']);

        $this->assertFalse($cart->deleteProduct(99));
        $this->assertTrue($cart->deleteProduct($skirt->sku));

        $this->assertFalse($cart->hasProduct($skirt->sku));
        $this->assertEquals(count($cart->getProducts($cart->id)), 0);
        $this->assertNull($cart->getProduct($skirt->sku));
    }

    public function testUpdateProductSku()
    {
        $cart = $this->user->createCart('My First Cart');
        $skirt = $cart->addProduct(0, 'Skirt', 10.00, 1, ['material' => 'Cotton']);
        $jeans = $cart->addProduct(1, 'Jeans', 10.00, 1, ['material' => 'Denim']);

        $this->assertFalse($cart->updateSkuFor($skirt->sku, $jeans->sku));
        $this->assertFalse($cart->updateSkuFor($jeans->sku, $skirt->sku));
        $this->assertFalse($cart->updateSkuFor(99, 100));

        $this->assertNotNull($skirt = $cart->updateSkuFor($skirt->sku, 2));

        $this->assertEquals($skirt->sku, 2);
        $this->assertEquals($skirt->name, 'Skirt');
        $this->assertEquals($skirt->unit_price, 10.00);
        $this->assertEquals($skirt->quantity, 1);
        $this->assertEquals($skirt->details->material, 'Cotton');
    }

    public function testUpdateProductName()
    {
        $cart = $this->user->createCart('My First Cart');
        $skirt = $cart->addProduct(0, 'Skirt', 10.00, 1, ['material' => 'Cotton']);

        $this->assertFalse($cart->updateNameFor(99, 'Black Skirt'));

        $this->assertNotNull($skirt = $cart->updateNameFor($skirt->sku, 'Black Skirt'));

        $this->assertEquals($skirt->sku, 0);
        $this->assertEquals($skirt->name, 'Black Skirt');
        $this->assertEquals($skirt->unit_price, 10.00);
        $this->assertEquals($skirt->quantity, 1);
        $this->assertEquals($skirt->details->material, 'Cotton');
    }

    public function testUpdateProductUnitPrice()
    {
        $cart = $this->user->createCart('My First Cart');
        $skirt = $cart->addProduct(0, 'Skirt', 10.00, 1, ['material' => 'Cotton']);

        $this->assertFalse($cart->updateUnitPriceFor(99, 15.00));

        $this->assertNotNull($skirt = $cart->updateUnitPriceFor($skirt->sku, 15.00));

        $this->assertEquals($skirt->sku, 0);
        $this->assertEquals($skirt->name, 'Skirt');
        $this->assertEquals($skirt->unit_price, 15.00);
        $this->assertEquals($skirt->quantity, 1);
        $this->assertEquals($skirt->details->material, 'Cotton');
    }

    public function testUpdateProductDetails()
    {
        $cart = $this->user->createCart('My First Cart');
        $skirt = $cart->addProduct(0, 'Skirt', 10.00, 1, ['material' => 'Cotton']);

        $this->assertFalse($cart->updateDetailsFor(99, ['materials' => ['Cotton', 'Elastan']]));

        $this->assertNotNull($skirt = $cart->updateDetailsFor($skirt->sku, ['materials' => ['Cotton', 'Elastan']]));

        $this->assertEquals($skirt->sku, 0);
        $this->assertEquals($skirt->name, 'Skirt');
        $this->assertEquals($skirt->unit_price, 10.00);
        $this->assertEquals($skirt->quantity, 1);
        $this->assertNotNull($skirt->details->materials);
        $this->assertEquals(count($skirt->details->materials), 2);
    }

    public function testUpdateProductQuantity()
    {
        $cart = $this->user->createCart('My First Cart');
        $skirt = $cart->addProduct(0, 'Skirt', 10.00, 1, ['material' => 'Cotton']);

        $this->assertFalse($cart->updateQuantityFor(99, 100));

        $this->assertNotNull($skirt = $cart->updateQuantityFor($skirt->sku, 100));

        $this->assertEquals($skirt->sku, 0);
        $this->assertEquals($skirt->name, 'Skirt');
        $this->assertEquals($skirt->unit_price, 10.00);
        $this->assertEquals($skirt->quantity, 100);
        $this->assertEquals($skirt->details->material, 'Cotton');
    }

    public function testProductTotal()
    {
        $cart = $this->user->createCart('My First Cart');
        $skirt = $cart->addProduct(0, 'Skirt', 10.00, 1, ['material' => 'Cotton']);

        $this->assertEquals($skirt->total(), $skirt->unit_price * $skirt->quantity);

        $cart->updateQuantityFor($skirt->sku, 100);
        $this->assertEquals($skirt->total(), $skirt->unit_price * $skirt->quantity);

        $cart->updateUnitPriceFor($skirt->sku, 100);
        $this->assertEquals($skirt->total(), $skirt->unit_price * $skirt->quantity);
    }

    public function testCartTotal()
    {
        $cart = $this->user->createCart('My First Cart');

        $this->assertEquals($cart->total(), 0.00);

        $skirt = $cart->addProduct(0, 'Skirt', 10.00, 1, ['material' => 'Cotton']);
        $jeans = $cart->addProduct(1, 'Jeans', 15.00, 10, ['material' => 'Cotton']);

        $this->assertEquals($cart->total(), ($skirt->unit_price * $skirt->quantity) + ($jeans->unit_price * $jeans->quantity));
    }

    public function testAddProductWhileAlreadyExisting()
    {
        $cart = $this->user->createCart('My First Cart');

        $skirt1 = $cart->addProduct(0, 'Skirt', 10.00, 1, ['material' => 'Cotton']);
        $skirt2 = $cart->addProduct(0, 'Skirt', 10.00, 1, ['material' => 'Cotton']);

        $this->assertTrue($cart->hasProduct($skirt1->sku));
        $this->assertTrue($cart->hasProduct($skirt2->sku));
        $this->assertEquals(count($cart->getProducts($cart->id)), 1);
        $this->assertNotNull($cart->getProduct($skirt1->sku));
        $this->assertNotNull($cart->getProduct($skirt2->sku));
    }

    public function testCoupon()
    {
        $cart = $this->user->createCart('My First Cart');

        $this->assertTrue($cart->setCoupon('CouponForCart'));
        $this->assertTrue($cart->hasCoupon());

        $cart = $cart->refresh();
        $this->assertEquals($cart->coupon_code, 'CouponForCart');
        $this->assertTrue($cart->deleteCoupon());
        $this->assertFalse($cart->hasCoupon());

        $this->assertTrue($cart->updateCoupon('CouponForCart'));
        $this->assertTrue($cart->hasCoupon());
        $this->assertEquals($cart->coupon_code, 'CouponForCart');

        $this->assertTrue($cart->updateCoupon('CouponForCart2'));
        $this->assertEquals($cart->coupon_code, 'CouponForCart2');
    }
}
