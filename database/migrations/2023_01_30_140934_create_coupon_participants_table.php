<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouponParticipantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cart_coupon_participants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('coupon_id')->nullable();
            $table->foreign('coupon_id')->references('id')->on('coupons');

            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');

            $table->unsignedBigInteger('cart_id')->nullable();
            $table->foreign('cart_id')->references('id')->on('carts');

            $table->unsignedBigInteger('main_participant_id')->nullable();
            $table->foreign('main_participant_id')->references('id')->on('users');

            $table->boolean('is_main_participant')->default(false);

            $table->string('share_coupon_code');

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
        Schema::dropIfExists('cart_coupon_participants');
    }
}
