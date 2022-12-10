<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDocumentsVerifedToStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->boolean('is_verified_logo')->nullable();
            $table->boolean('is_verified_cover')->nullable();
            $table->boolean('is_verified_licence')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn('is_verified_logo');
            $table->dropColumn('is_verified_cover');
            $table->dropColumn('is_verified_licence');
        });
    }
}
