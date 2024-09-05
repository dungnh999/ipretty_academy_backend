<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePushNotificationsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('push_notifications', function (Blueprint $table) {
            $table->increments('notification_id');
            $table->boolean('isPublished')->default(0);
            $table->bigInteger('created_by')->unsigned();
            $table->enum('notification_cat', ['AD', 'DOC', 'FUNC', 'HOL', 'POL'])->nullable();
            $table->string('notification_title');
            $table->string('group_receivers')->nullable();
            $table->longText('notification_message')->nullable();
            $table->timestamps();
            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('push_notifications');
    }
}
