<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTargetOfferMilestonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('target_offer_milestones', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('target_offer_id');
            $table->float('targeted_price');
            $table->float('reward_value');
            $table->boolean('is_active');
            $table->timestamps();
            $table->foreign('target_offer_id')->references('id')->on('target_offers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('target_offer_milestones');
    }
}
