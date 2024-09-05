<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->increments('question_id');
            $table->text('question_title');
            $table->longText('question_description')->nullable();
            $table->enum('question_type',['MultipleChoice', 'SingleChoice'])->default('MultipleChoice');
            $table->integer('number_order')->nullable()->default(0);
            $table->longText('question_attachments')->nullable();
            $table->boolean('has_attachment')->nullable()->default(false);
            $table->integer('session_id')->unsigned()->nullable();
            $table->foreign('session_id')->references('session_id')->on('sessions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('questions');
    }
}
