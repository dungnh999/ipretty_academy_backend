<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColoumnDeleteMessengerForUserMessageStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_message_statuses', function (Blueprint $table) {
            $table->integer('delete_id_mess')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_message_statuses', function (Blueprint $table) {
            $table->dropColumn(['delete_id_mess']);
        });
    }
}
