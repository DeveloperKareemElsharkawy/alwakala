<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComplaintsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone');
            $table->text('details');
            $table->unsignedInteger('complaint_topic_id');
            $table->foreign('complaint_topic_id')->references('id')->on('complaint_topics');
            $table->unsignedInteger('app_id');
            $table->foreign('app_id')->references('id')->on('apps');
            $table->timestamps();
            $table->index(['complaint_topic_id', 'app_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('complaints');
    }
}
