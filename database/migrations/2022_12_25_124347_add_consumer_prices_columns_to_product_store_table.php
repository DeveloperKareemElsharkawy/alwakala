<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConsumerPricesColumnsToProductStoreTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_store', function (Blueprint $table) {
            $table->float('consumer_old_price')->nullable();
            $table->float('consumer_price_discount')->nullable();
            $table->enum('consumer_price_discount_type', ['1', '2'])->nullable();
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
            $table->dropColumn('consumer_old_price');
            $table->dropColumn('consumer_price_discount');
            $table->dropColumn('consumer_price_discount_type');
        });
    }
}
