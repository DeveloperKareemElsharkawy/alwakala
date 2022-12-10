<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProductStore extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_store', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('product_id');
            $table->integer('store_id');

            $table->integer('views');
            $table->date('publish_app_at');

            $table->float('price')->nullable();
            $table->float('net_price')->nullable();
            $table->float('consumer_price')->nullable();

            $table->float('original_supplier_price')->nullable();
            $table->float('original_consumer_price')->nullable();

            $table->float('discount')->nullable();
            $table->enum('discount_type', ['1', '2']); // 1 amount, 2 percentage;

            $table->boolean('activation')->default(1);
            $table->boolean('is_purchased')->default(0);
            $table->boolean('free_shipping')->default(false);

            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('store_id')->references('id')->on('stores');

            $table->timestamps();
            $table->index(['product_id', 'store_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_store', function (Blueprint $table) {
            //
        });
    }
}
