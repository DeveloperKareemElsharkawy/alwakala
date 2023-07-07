<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOfferProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offer_product', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('offer_id');
            $table->unsignedInteger('product_id');
            $table->timestamps();
            $table->foreign('offer_id')->references('id')->on('offers')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products');
            $table->index(['offer_id', 'product_id']);
            $table->softDeletes();


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('offer_product');
    }
}
