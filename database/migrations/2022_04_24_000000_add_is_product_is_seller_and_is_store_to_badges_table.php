<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsProductIsSellerAndIsStoreToBadgesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('badges', function (Blueprint $table) {
            $table->boolean('is_product')->default(true)->after('icon');
            $table->boolean('is_seller')->default(true)->after('is_product');
            $table->boolean('is_store')->default(true)->after('is_seller');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('is_product');
            $table->dropColumn('is_seller');
            $table->dropColumn('is_store');
        });
    }
}
