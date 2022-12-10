<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ShoppingCartProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_shopping_cart', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('product_id');
            $table->bigInteger('shopping_cart_id');
            $table->bigInteger('store_id');
            $table->bigInteger('packing_unit_id');
            $table->bigInteger('size_id')->nullable();
            $table->bigInteger('purchased_item_count')->default(1);
            $table->float('item_price');
            $table->float('total_price');
            $table->bigInteger('basic_unit_count');
            $table->unsignedInteger('color_id');
            $table->timestamps();
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('color_id')->references('id')->on('colors');
            $table->foreign('store_id')->references('id')->on('stores');
            $table->foreign('shopping_cart_id')->references('id')->on('shopping_carts')->onDelete('cascade');
            $table->foreign('packing_unit_id')->references('id')->on('packing_units');
            $table->foreign('size_id')->references('id')->on('sizes');
            $table->index(['product_id', 'color_id', 'store_id', 'shopping_cart_id', 'packing_unit_id', 'size_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_shopping_cart');
    }
}
