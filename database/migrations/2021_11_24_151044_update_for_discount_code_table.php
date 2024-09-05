<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateForDiscountCodeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('discount_code', function (Blueprint $table) {
            $table->string('title');
            $table->dateTime('time_start');
        });
        Schema::table('discount_code', function (Blueprint $table) {
            $table->dropColumn(['description']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('discount_code', function (Blueprint $table) {
            $table->dropColumn(['description']);
            $table->dropColumn(['time_start']);
        });
    }
}
