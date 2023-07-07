<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouponesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('code')->unique();
            $table->text('images')->nullable();

            $table->unsignedBigInteger('seller_id')->index();
            $table->foreign('seller_id')->references('id')->on('users');

            $table->unsignedBigInteger('brand_id')->nullable();
            $table->foreign('brand_id')->references('id')->on('brands');



            $table->tinyInteger('max_usage_count')->default(0);
            $table->tinyInteger('current_usage_count')->default(0);


            $table->tinyInteger('is_active')->default(0);


            $table->date('start_date')->nullable();
            $table->time('start_time')->nullable();

            $table->date('end_date')->nullable();
            $table->time('end_time')->nullable();

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
        Schema::dropIfExists('coupons');
    }
}
