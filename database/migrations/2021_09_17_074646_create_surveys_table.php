<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSurveysTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('surveys', function (Blueprint $table) {
            $table->increments('survey_id');
            $table->string('survey_title');
            $table->longText('survey_description')->nullable();
            $table->bigInteger('created_by')->unsigned()->nullable();
            $table->bigInteger('survey_duration')->nullable()->default(0);
            $table->integer('percent_to_pass')->nullable()->default(0);
            $table->integer('question_per_page')->nullable()->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('surveys');
    }
}
