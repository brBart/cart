<?php

namespace Rennokki\Cart;

use Illuminate\Support\ServiceProvider;

class CartServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/cart.php' => config_path('cart.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/../database/migrations/2018_07_01_123200_cart.php' => database_path('migrations/2018_07_01_123200_cart.php'),
        ], 'migration');
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
