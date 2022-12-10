<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductPackingUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packing_unit_product', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('packing_unit_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('basic_unit_id');
            $table->integer('basic_unit_count');
            $table->timestamps();
            $table->foreign('basic_unit_id')->references('id')->on('packing_units');
            $table->foreign('packing_unit_id')->references('id')->on('packing_units');
            $table->foreign('product_id')->references('id')->on('products');
            $table->index(['basic_unit_id', 'packing_unit_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('packing_unit_products');
    }
}
