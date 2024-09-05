<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnswersTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('answers', function (Blueprint $table) {
            $table->increments('answer_id');
            $table->integer('question_id')->unsigned()->nullable();
            $table->integer('option_id')->unsigned()->nullable();
            $table->bigInteger('answer_by')->unsigned()->nullable();
            $table->integer('survey_id')->unsigned()->nullable();
            $table->timestamps();
            $table->foreign('question_id')->references('question_id')->on('questions');
            $table->foreign('option_id')->references('option_id')->on('question_options');
            $table->foreign('answer_by')->references('id')->on('users');
            $table->foreign('survey_id')->references('survey_id')->on('surveys');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('answers');
    }
}
