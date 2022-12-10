<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('home_sections', function (Blueprint $table) {
            $table->dropColumn('api');
            $table->dropColumn('type');
            $table->string('items_ids',1024)->nullable();
            $table->integer('app_type')->nullable();
            $table->integer('order')->nullable();
            $table->integer('item_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('home_sections', function (Blueprint $table) {
            $table->string('api')->nullable();
            $table->integer('type')->nullable();
            $table->dropColumn('items_ids');
            $table->dropColumn('app_type');
            $table->dropColumn('item_type');
            $table->dropColumn('order');
        });
    }
}
