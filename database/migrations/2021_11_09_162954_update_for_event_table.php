<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateForEventTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->enum('status_reminder', ['no_repeat','repeat_week','option'])->default('no_repeat');
            $table->enum('color', ['1','2','3','4','5','6','7','8'])->default('1');
            $table->dateTime('time_end')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['status_reminder']);
            $table->dropColumn(['time_end']);
            $table->dropColumn(['color']);
        });
    }
}
