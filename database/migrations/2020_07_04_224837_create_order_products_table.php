<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->float('item_price');
            $table->float('total_price');
            $table->float('basic_unit_count');
            $table->unsignedInteger('order_id');
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('store_id');
            $table->unsignedInteger('purchased_item_count');
            $table->unsignedInteger('quantity');
            $table->unsignedInteger('size_id')->nullable();
            $table->unsignedInteger('color_id');
            $table->unsignedInteger('status_id')->default(1);
            $table->timestamps();
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('size_id')->references('id')->on('sizes');
            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreign('color_id')->references('id')->on('colors');
            $table->foreign('store_id')->references('id')->on('stores');
            $table->foreign('status_id')->references('id')->on('order_statuses');
            $table->index(['product_id', 'size_id', 'order_id', 'color_id', 'store_id', 'status_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_order_products');
    }
}
