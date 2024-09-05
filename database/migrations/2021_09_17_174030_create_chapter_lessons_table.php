<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChapterLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chapters_lessons', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('chapter_id')->unsigned()->nullable();
            $table->integer('lesson_id')->unsigned()->nullable();
            $table->foreign('chapter_id')->references('chapter_id')->on('chapters');
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
        Schema::drop('chapters_lessons');

    }
}
