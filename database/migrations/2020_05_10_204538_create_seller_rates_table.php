<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSellerRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seller_rates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->morphs('rater');
            $table->morphs('rated');
            $table->unsignedInteger('rated_store_id	');
            $table->foreign('rated_store_id')->references('id')->on('stores');
            $table->integer('rate');
            $table->string('review');
            $table->text('images')->nullable();
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
        Schema::dropIfExists('seller_rates');
    }
}
