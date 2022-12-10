<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('status_ar');
            $table->string('status_en');
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->float('total_price');
            $table->float('discount');
            $table->string('number')->nullable();
            $table->unsignedInteger('status_id')->default(1);
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('payment_method_id')->nullable();
            $table->unsignedInteger('order_address_id')->nullable();
            $table->foreign('order_address_id')->references('id')->on('order_addresses');
            $table->foreign('payment_method_id')->references('id')->on('payment_methods');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('status_id')->references('id')->on('order_statuses');
            $table->timestamps();
            $table->index(['payment_method_id', 'user_id', 'status_id', 'order_address_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
