<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionAuthorizedOwnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_authorized_owners', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->morphs('transaction'); //points or money
            $table->unsignedInteger('stores_owner_id')->comment('owner of multiple stores');
            $table->timestamps();
            $table->foreign('stores_owner_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaction_authorized_owners');
    }
}
