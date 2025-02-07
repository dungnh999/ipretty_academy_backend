<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseLessonChapterTable extends Migration
{
    public function up()
    {
        Schema::create('course_lesson_chapter', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();  // Thêm trường uuid
            $table->unsignedBigInteger('course_id');  // Khóa ngoại đến course
            $table->unsignedBigInteger('chapter_id');  // Khóa ngoại đến chapter
            $table->unsignedBigInteger('lesson_id');  // Khóa ngoại đến lesson
            $table->integer('priority');  // Mức độ ưu tiên
            $table->integer('position');  // Vị trí trong chương học
            $table->timestamps();
//
//            // Khóa ngoại
//            $table->foreign('course_id')->references('course_id')->on('courses')->onDelete('cascade');
//            $table->foreign('chapter_id')->references('chapter_id')->on('chapters')->onDelete('cascade');
//            $table->foreign('lesson_id')->references('lesson_id')->on('lessons')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('course_lesson_chapter');
    }
}
