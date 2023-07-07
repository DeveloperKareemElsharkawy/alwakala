<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfferDiscountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offer_purchases', function (Blueprint $table) {
            $table->id();

            $table->foreignId('offer_id')->constrained('offers');

            $table->float('price_from');
            $table->float('price_to');

            $table->float('discount_value');

            $table->timestamps();
        });

        Schema::create('offer_bundles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('offer_id')->constrained('offers');

            $table->float('from');
            $table->float('to');

            $table->float('discount_value');

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
        Schema::dropIfExists('offer_discounts');
    }
}
