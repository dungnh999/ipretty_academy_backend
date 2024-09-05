<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveSessionIdAddSurveyIdQuestions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('questions', function (Blueprint $table) {
            if (Schema::hasColumn('questions', 'session_id')) {
                $table->dropForeign(['session_id']);
                $table->dropColumn(['session_id']);
            }
            $table->integer('survey_id')->unsigned()->nullable();
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
        Schema::table('questions', function (Blueprint $table) {
            if (Schema::hasColumn('questions', 'survey_id')) {
                $table->dropForeign(['survey_id']);
                $table->dropColumn(['survey_id']);
            }
            $table->integer('session_id')->unsigned()->nullable();
            $table->foreign('session_id')->references('session_id')->on('sessions');
        });
    }
}
