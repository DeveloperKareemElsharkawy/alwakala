<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShippingCompanyLineDaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipping_company_line_days', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('day_of_week_id');
            $table->unsignedInteger('shipping_company_line_id');
            $table->foreign('shipping_company_line_id')->references('id')->on('shipping_company_lines')->onDelete('cascade');
            $table->foreign('day_of_week_id')->references('id')->on('days_of_week')->onDelete('cascade');
            $table->index(['shipping_company_line_id', 'day_of_week_id']);
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
        Schema::dropIfExists('shipping_company_line_days');
    }
}
