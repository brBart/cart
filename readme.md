[![Build Status](https://travis-ci.org/rennokki/cart.svg?branch=master)](https://travis-ci.org/rennokki/cart)
[![codecov](https://codecov.io/gh/rennokki/cart/branch/master/graph/badge.svg)](https://codecov.io/gh/rennokki/cart/branch/master)
[![StyleCI](https://github.styleci.io/repos/136514812/shield?branch=master)](https://github.styleci.io/repos/136514812)
[![Latest Stable Version](https://poser.pugx.org/rennokki/cart/v/stable)](https://packagist.org/packages/rennokki/cart)
[![Total Downloads](https://poser.pugx.org/rennokki/cart/downloads)](https://packagist.org/packages/rennokki/cart)
[![Monthly Downloads](https://poser.pugx.org/rennokki/cart/d/monthly)](https://packagist.org/packages/rennokki/cart)
[![License](https://poser.pugx.org/rennokki/cart/license)](https://packagist.org/packages/rennokki/cart)

[![PayPal](https://img.shields.io/badge/PayPal-donate-blue.svg)](https://paypal.me/rennokki)

# Laravel Cart
Laravel Cart is a package that helps building a cart-like system for online stores. You can store carts, products and you can add new products, update the existing ones or delete them.

# Installation
You have to install the package via Composer CLI:
```bash
$ composer require rennokki/guardian
```

If your Laravel version does not come with package auto-discovery, feel free to add this line in your `providers` array from `config/app.php`:
```php
Rennokki\Guardian\CartServiceProvider::class,
```

Publish the config and the migration:
```bash
$ php artisan vendor:publish
```

Then update the database via the Artisan `migrate` command:
```bash
$ php artisan migrate
```

On the Model level, you have to implement the following trait:
```php
use Rennokki\Cart\Traits\HasCarts;

class User extends Model {
    use HasCarts;
    ...
}
```

# Creating a Cart
You can now create carts for any user. Let's create one:
```php
$cart = $user->createCart('My Cart');
```

With that `CartModel` instance, you can do more. You can add, remove, update product IDs (SKUs), names, attributes, quantity and unit prices.

# Adding items to Cart
Let's add our first item. It's going to be a `Skirt`, costing `15.00` (no currency involved), `5` of them, and the `material` attribute is set to `Cotton`.
```php
$skirt = $cart->addProduct('my-unique-sku', 'Skirt', 15.00, 5, ['material' => 'Cotton']); // Returns a CartProductModel instance.
```

If you add products on one another, with the same SKU, it will:
* Update the quantity as the sum between the existing one and the one present in the method. (i.e. You have 3 in your cart, adding 2 of the same ones will get 5 in total)
* Update any name, unit price and attributes ONLY if they are different.
```php
$skirt = $cart->addProduct('my-unique-sku', 'Skirt', 15.00, 5, ['material' => 'Cotton']);
$cart->addProduct('my-unique-sku', 'Skirt', 15.00, 5, ['material' => 'Cotton']);

$cart->getProduct($skirt->sku)->quantity; // This will get you 10 (5+5)
```

# Updating Cart products
If you plan to update the item from `Skirt` to `Black Skirt`, you can do it likewise:
```php
$skirt = $cart->updateNameFor($skirt->sku, 'Black Skirt');
```

It works the same way with quantity, unit price and attributes:
```php
$skirt = $cart->updateUnitPriceFor($skirt->sku, 20.00);
$skirt = $cart->updateQuantityFor($skirt->sku, 1);
$skirt = $cart->updateAttributesFor($skirt->sku, ['materials' => ['Cotton', 'Elastan']]);
```

# Getting Cart total
On each Cart instance, you can get the total price using one simple method:
```php
$skirt->total(); // 75.00
```

# Getters for Cart
```php
$cart->getProducts(); // Returns a collection or an empty array.
$cart->getProduct('my-sku-here'); // Retuns either null or a Product instance.
$cart->hasProduct('my-sku-here'); // Returns true if the cart has the product.
$cart->isEmpty(); // true, if it has no products
```

# Updating products' SKUs
If you want to update the SKU during mid-cart, you have to keep in mind that the SKU should not exist in the cart already, otherwise it will return `false`.
```php
$skirt = $cart->updateSkuFor($skirt->sku, 'new-sku);
```

# Deleting products
```
$cart->deleteProduct($skirt->sku);
```

# Relationships
```php
$cart->products();
$skirt->cart(); 
```
