<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFaqIdCommentsFaq extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('comments_faq', function (Blueprint $table) {
            $table->integer('question_id')->unsigned()->nullable();
            $table->foreign('question_id')->references('question_id')->on('faq_questions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('comments_faq', function (Blueprint $table) {
            $table->dropForeign(['question_id']);
            $table->dropColumn(['question_id']);
        });
    }
}
