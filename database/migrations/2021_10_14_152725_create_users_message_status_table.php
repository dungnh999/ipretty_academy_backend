<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersMessageStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_message_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->Biginteger('user_id')->unsigned()->index()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->Biginteger('partner_id')->unsigned()->index()->nullable();
            $table->foreign('partner_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('lasted_message_seen_id')->unsigned()->index()->nullable();
            $table->foreign('lasted_message_seen_id')->references('id')->on('messages')->onDelete('cascade');
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
        Schema::dropIfExists('user_message_statuses');
    }
}
