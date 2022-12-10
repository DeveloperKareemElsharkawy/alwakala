<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('logo')->nullable();
            $table->string('licence')->nullable();
            $table->string('address');
            $table->string('landing_number')->nullable();
            $table->string('mobile');
            $table->float('latitude');
            $table->float('longitude');
            $table->integer('building_no')->nullable();
            $table->string('landmark')->nullable();
            $table->string('main_street')->nullable();
            $table->string('side_street')->nullable();
            $table->string('legal_name')->nullable();
            $table->boolean('is_store_has_delivery')->default(false);
            $table->text('description')->nullable();
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('store_type_id');
            $table->unsignedInteger('city_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('store_type_id')->references('id')->on('store_types')->onDelete('cascade');
            $table->foreign('city_id')->references('id')->on('cities');
            $table->text('feed_link')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->string('cover')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->boolean('is_main_branch')->default(true);

//            $table->integer('delivery_days')->nullable();
//            $table->integer('delivery_hours')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'store_type_id', 'city_id']);
            //store rate in store_rate file
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stores');
    }
}
