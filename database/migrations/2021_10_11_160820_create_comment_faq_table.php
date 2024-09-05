<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentFaqTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments_faq', function (Blueprint $table) {
            $table->increments('id');
            $table->Biginteger('commentator_id')->unsigned();
            $table->text('comment')->collate()->nullable();
            $table->integer('faq_id')->unsigned()->nullable();
            $table->enum('comment_type', ['Text', 'File', 'Image'])->collate()->default('Text');
            $table->text('file_name')->collate()->nullable();
            $table->text('file_url')->collate()->nullable();
            $table->integer('parent_id')->nullable();
            $table->foreign('commentator_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('faq_id')->references('id')->on('frequently_asked_questions');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comments_faq');
    }
}
