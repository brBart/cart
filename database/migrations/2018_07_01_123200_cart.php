<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Cart extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('model_id');
            $table->string('model_type');

            $table->string('name');

            $table->timestamps();
        });

        Schema::create('carts_products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cart_id');

            $table->string('sku');
            $table->string('name');
            $table->float('unit_price', 9, 2);
            $table->integer('quantity');

            $table->text('attributes');

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('carts');
        Schema::dropIfExists('carts_products');
    }
}
