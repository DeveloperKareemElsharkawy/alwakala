<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppTvsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_tvs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('image');
            $table->integer('item_id');
            $table->unsignedInteger('item_type');
            $table->foreign('item_type')->references('id')->on('app_tv_types');
            $table->timestamp('expiry_date');
            $table->unsignedInteger('app_id')->nullable();
            $table->foreign('app_id')->references('id')->on('apps');
            $table->unsignedInteger('category_id')->nullable();
            $table->foreign('category_id')->references('id')->on('categories');
            $table->unsignedInteger('store_id')->nullable();
            $table->foreign('store_id')->references('id')->on('stores');
            $table->timestamps();
            $table->index(['item_type', 'store_id', 'category_id', 'item_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('app_tvs');
    }
}
