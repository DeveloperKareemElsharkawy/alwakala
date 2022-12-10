<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoreOpeningHoursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_opening_hours', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('store_id');
            $table->unsignedInteger('days_of_week_id');
            $table->time('open_time')->nullable();
            $table->time('close_time')->nullable();
            $table->foreign('store_id')->references('id')->on('stores');
            $table->foreign('days_of_week_id')->references('id')->on('days_of_week');
            $table->boolean('is_open');
            $table->timestamps();
            $table->index('store_id', 'days_of_week_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('store_opening_hours');
    }
}
