<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableFrequentlyAskedQuestions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('frequently_asked_questions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('body');
            $table->bigInteger('created_by')->unsigned()->nullable();
            $table->integer('type_category_id')->unsigned()->nullable();
            $table->string('attachments');
            $table->timestamps();
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('type_category_id')->references('id')->on('faq_category');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('frequently_asked_questions');
    }
}
