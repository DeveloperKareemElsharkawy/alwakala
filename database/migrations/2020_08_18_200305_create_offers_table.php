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

            $table->string('name_ar')->nullable();
            $table->string('name_en')->nullable();

            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();

            $table->text('image')->nullable();

            $table->date('start_date')->nullable();
            $table->time('start_time')->nullable();

            $table->date('end_date')->nullable();
            $table->time('end_time')->nullable();

            $table->enum('type', ['purchases', 'bundles']);
            $table->double('target', 9, 3);

            $table->boolean('is_active')->default(true);


            $table->enum('discount_type', ['1', '2']); // 1 amount, 2 percentage;
            $table->float('discount_value');


            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('store_id')->constrained('stores');

            $table->softDeletes();
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
        Schema::dropIfExists('offers');
    }
}
