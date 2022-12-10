<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShippingCompanyLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipping_company_lines', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->boolean('is_deliver_on_hand');
            $table->unsignedInteger('shipping_company_id');
            $table->unsignedInteger('from_shipping_area_id');
            $table->unsignedInteger('to_shipping_area_id');
            $table->foreign('shipping_company_id')->references('id')->on('shipping_companies')->onDelete('cascade');
            $table->foreign('from_shipping_area_id')->references('id')->on('shipping_areas')->onDelete('cascade');
            $table->foreign('to_shipping_area_id')->references('id')->on('shipping_areas')->onDelete('cascade');
            $table->index(['shipping_company_id', 'from_shipping_area_id', 'to_shipping_area_id']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shipping_company_lines');
    }
}
