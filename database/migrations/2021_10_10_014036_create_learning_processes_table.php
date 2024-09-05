<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLearningProcessesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('learning_processes', function (Blueprint $table) {
            $table->increments('process_id');
            $table->integer('lesson_id')->unsigned()->nullable();
            $table->integer('survey_id')->unsigned()->nullable();
            $table->integer('course_id')->unsigned()->nullable();
            $table->bigInteger('student_id')->unsigned()->nullable();
            $table->integer('process')->nullable()->default(0);
            $table->boolean('isPassed')->default(false);
            $table->boolean('isDraft')->default(false);
            $table->dateTime('completed_at')->nullable();
            $table->dateTime('started_at')->nullable();
            $table->foreign('lesson_id')->references('lesson_id')->on('lessons');
            $table->foreign('survey_id')->references('survey_id')->on('surveys');
            $table->foreign('course_id')->references('course_id')->on('courses');
            $table->foreign('student_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('learning_processes');
    }
}
