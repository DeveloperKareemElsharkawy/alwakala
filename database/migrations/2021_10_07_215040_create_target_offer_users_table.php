<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTargetOfferUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('target_offer_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('target_offer_id');
            $table->unsignedInteger('receiver_user_id');
            $table->boolean('is_approved')->default(false);
            $table->unsignedInteger('achieved_milestone_id')->nullable();
            $table->unsignedInteger('targeted_milestone_id')->nullable();
            $table->timestamps();
            $table->foreign('target_offer_id')->references('id')->on('target_offers');
            $table->foreign('receiver_user_id')->references('id')->on('users');
            $table->foreign('achieved_milestone_id')->references('id')->on('target_offer_milestones');
            $table->foreign('targeted_milestone_id')->references('id')->on('target_offer_milestones');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('target_offer_users');
    }
}
