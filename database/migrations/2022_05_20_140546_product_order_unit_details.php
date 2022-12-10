<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ProductOrderUnitDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_order_unit_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('order_product_id');
            $table->integer('size_id');
            $table->integer('quantity');
            $table->foreign('order_product_id')->references('id')->on('order_products');
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
        Schema::dropIfExists('product_order_unit_details');
    }
}
