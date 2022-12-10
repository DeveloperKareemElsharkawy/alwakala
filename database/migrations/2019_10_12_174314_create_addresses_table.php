<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->string('address')->nullable();
            $table->float('latitude')->default(0.0);
            $table->float('longitude')->default(0.0);
            $table->integer('building_no')->nullable();
            $table->string('landmark')->nullable();
            $table->string('main_street')->nullable();
            $table->string('side_street')->nullable();
            $table->string('mobile')->nullable();
            $table->boolean('is_default')->default('f');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('city_id')->nullable();

            $table->foreign('city_id')->references('id')->on('cities');
            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('addresses');
    }
}
