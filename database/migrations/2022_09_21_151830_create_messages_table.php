<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();

            $table->longText('message')->nullable();
            $table->string('image')->nullable();
            $table->string('video')->nullable();
            $table->string('record')->nullable();

            $table->unsignedBigInteger('conversation_id')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();

            $table->boolean('is_seen')->default(0);

            $table->unsignedBigInteger('sender_id');
            $table->unsignedBigInteger('receiver_id');

            $table->unsignedBigInteger('store_sender_id');
            $table->unsignedBigInteger('store_receiver_id');

            $table->foreign('store_sender_id')->references('id')->on('stores');
            $table->foreign('store_receiver_id')->references('id')->on('stores');


            $table->boolean('deleted_from_sender')->default(0);
            $table->boolean('deleted_from_receiver')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('messages');
    }
}
