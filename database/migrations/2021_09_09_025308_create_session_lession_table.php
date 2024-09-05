<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSessionLessionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sessions_lessons', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('session_id')->unsigned()->nullable();
            $table->integer('lesson_id')->unsigned()->nullable();
            $table->integer('count_views')->nullable()->default(0);
            $table->softDeletes();
            $table->foreign('session_id')->references('session_id')->on('sessions');
            $table->foreign('lesson_id')->references('lesson_id')->on('lessons');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('sessions_lessons');
    }
}
