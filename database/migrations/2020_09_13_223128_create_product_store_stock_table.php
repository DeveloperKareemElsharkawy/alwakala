<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductStoreStockTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_store_stock', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('product_store_id');
            $table->integer('stock');
            $table->integer('reserved_stock');
            $table->integer('available_stock');
            $table->integer('sold');
            $table->integer('returned');
            $table->unsignedInteger('size_id')->nullable();
            $table->unsignedInteger('color_id');
            $table->boolean('approved')->nullable()->default(0);
            $table->timestamps();
            $table->foreign('product_store_id')->references('id')->on('product_store');
            $table->foreign('size_id')->references('id')->on('sizes');
            $table->foreign('color_id')->references('id')->on('colors');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_store_stock');
    }
}
