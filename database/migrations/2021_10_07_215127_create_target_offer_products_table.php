<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTargetOfferProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('target_offer_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('target_id');
            $table->unsignedInteger('product_id');
            $table->timestamps();
            $table->foreign('target_id')->references('id')->on('target_offers');
            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('target_offer_products');
    }
}
