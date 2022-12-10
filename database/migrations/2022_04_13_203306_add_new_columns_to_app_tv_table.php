<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnsToAppTvTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('app_tvs', function (Blueprint $table) {
            $table->unsignedBigInteger('home_section_id')->nullable();
            $table->text('items_ids')->nullable();
            $table->string('item_id')->nullable()->change();
            $table->string('title_en')->nullable();
            $table->string('title_ar')->nullable();
            $table->string('description_en')->nullable();
            $table->string('description_ar')->nullable();
            $table->string('mobile_image')->nullable();
            $table->renameColumn('image', 'web_image');
            $table->integer('order')->nullable();


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('app_tvs', function (Blueprint $table) {
            $table->dropColumn('home_section_id');
            $table->dropColumn('items_ids');
            $table->dropColumn('title_en');
            $table->dropColumn('title_ar');
            $table->dropColumn('description_en');
            $table->dropColumn('description_ar');
            $table->dropColumn('mobile_image');
            $table->dropColumn('order');
            $table->renameColumn('web_image', 'image');

        });
    }
}
