<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name');
             $table->string('image')->nullable();
            $table->text('description')->nullable();

            $table->integer('max_usage_count')->nullable();
            $table->unsignedInteger('type_id');

            $table->unsignedInteger('user_id');

            $table->float('discount_value')->default(0);
            $table->enum('discount_type', [1, 2, 3])->default(1); // 1 amount, 2 percentage, 3 goods

            $table->float('total_price')->nullable();
            $table->float('bulk_price')->nullable();
            $table->integer('total_purchased_items')->nullable();

            $table->timestamp('from')->nullable();
            $table->timestamp('to')->nullable();
            $table->tinyInteger('has_end_date')->default(1);

            $table->boolean('activation')->default(false);

            $table->timestamps();
            $table->foreign('type_id')->references('id')->on('offer_types');
            $table->foreign('user_id')->references('id')->on('users');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('offers');
    }
}
