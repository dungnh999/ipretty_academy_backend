<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->increments('id');
            $table->text('title');
            $table->text('description');
            $table->integer('course_id')->unsigned()->nullable();
            $table->foreign('course_id')->references('course_id')->on('courses');
            $table->dateTime('time_start')->nullable();
            $table->Biginteger('create_by')->unsigned()->index()->nullable();
            $table->foreign('create_by')->references('id')->on('users')->onDelete('cascade');
            $table->integer('distance_time_reminder')->nullable()->default(0);
            $table->integer('distance_time_reminder_2')->nullable()->default(0);
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
        Schema::dropIfExists('events');
    }
}
