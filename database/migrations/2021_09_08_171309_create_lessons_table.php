<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLessonsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->increments('lesson_id');
            $table->string('lesson_name');
            $table->longText('lesson_description')->nullable();
            $table->longText('lesson_content')->nullable();
            $table->text('lesson_attachment')->nullable();
            $table->bigInteger('lesson_author')->unsigned()->nullable();
            $table->enum('lesson_status', ['Draft', 'Publish'])->default('Publish');
            $table->longText('main_attachment')->nullable();
            $table->string('lesson_duration')->nullable();
            $table->integer('total_views')->nullable()->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('lesson_author')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('lessons');
    }
}
