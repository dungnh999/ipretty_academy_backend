<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveForeignLikeCmt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('faq_likes', function (Blueprint $table) {
            $table->dropForeign(['faq_id']);
            $table->dropColumn(['faq_id']);
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
        Schema::table('faq_likes', function (Blueprint $table) {
            $table->dropForeign(['question_id']);
            $table->dropColumn(['question_id']);
            $table->integer('faq_id')->unsigned()->nullable();
            $table->foreign('faq_id')->references('id')->on('frequently_asked_questions');
        });
    }
}
