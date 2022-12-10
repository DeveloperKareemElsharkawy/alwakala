<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComplaintTopicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('complaint_topics', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name_ar');
            $table->string('name_en');
            $table->timestamps();
        });
        DB::table('complaint_topics')->insert([
            [
                'name_ar' => 'Stores',
                'name_en' => 'المتاجر',
            ],
            [
                'name_ar' => 'Products',
                'name_en' => 'المنتجات',
            ],
            [
                'name_ar' => 'Shipping Companies',
                'name_en' => 'شركات الشحن',
            ],
            [
                'name_ar' => 'Orders',
                'name_en' => 'الطلبات',
            ],
            [
                'name_ar' => 'Other',
                'name_en' => 'استفسارات اخري',
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('complaint_topics');
    }
}
