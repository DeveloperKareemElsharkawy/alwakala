<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHasChangedColumnToStoreMobileChangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('store_mobile_changes', function (Blueprint $table) {
            $table->boolean('has_changed')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('store_mobile_changes', function (Blueprint $table) {
            $table->dropColumn('has_changed');
        });
    }
}
