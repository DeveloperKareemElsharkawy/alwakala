<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackingUnitProductAttributesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packing_unit_product_attributes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('size_id');
            $table->integer('quantity');
            $table->unsignedInteger('packing_unit_product_id');
            $table->timestamps();
            $table->foreign('packing_unit_product_id')->references('id')->on('packing_unit_product');
            $table->foreign('size_id')->references('id')->on('sizes');
            $table->index(['packing_unit_product_id', 'size_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('packing_unit_product_store_attributes');
    }
}
