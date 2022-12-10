<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouponDiscountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupon_discounts', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('coupon_id')->index();
            $table->foreign('coupon_id')->references('id')->on('coupons');

            $table->float('amount_from')->nullable();
            $table->float('amount_to')->nullable();

            $table->enum('discount_type', ['1', '2']);
            $table->float('discount')->nullable();

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
        Schema::dropIfExists('coupon_discounts');
    }
}
