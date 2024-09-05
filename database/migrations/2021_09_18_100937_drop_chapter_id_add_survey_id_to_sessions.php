<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropChapterIdAddSurveyIdToSessions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sessions', function (Blueprint $table) {
            if (Schema::hasColumn('sessions', 'chapter_id')) {
                $table->dropForeign(['chapter_id']);
                $table->dropColumn(['chapter_id']);
            }
            $table->integer('survey_id')->unsigned()->nullable();
            $table->foreign('survey_id')->references('survey_id')->on('surveys');
        });
        
        if (Schema::hasTable('sessions_lessons')) {
            Schema::drop('sessions_lessons');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sessions', function (Blueprint $table) {
            if (Schema::hasColumn('sessions', 'survey_id')) {
                $table->dropForeign(['survey_id']);
                $table->dropColumn(['survey_id']);
            }
            $table->integer('chapter_id')->unsigned()->nullable();
            $table->foreign('chapter_id')->references('chapter_id')->on('chapters');
        });

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
}
