<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFaqQuestionsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('faq_questions', function (Blueprint $table) {
            $table->increments('question_id');
            $table->longText('question_name')->nullable();
            $table->longText('answer_name')->nullable();
            $table->integer('number_order')->nullable()->default(0);
            $table->integer('faq_id')->unsigned()->nullable();
            $table->softDeletes();
            $table->foreign('faq_id')->references('id')->on('frequently_asked_questions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('faq_questions');
    }
}
