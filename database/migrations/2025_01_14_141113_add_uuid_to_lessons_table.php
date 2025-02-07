<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUuidToLessonsTable extends Migration
{
    public function up()
    {
//        Schema::table('lessons', function (Blueprint $table) {
//            $table->uuid('uuid')->unique()->nullable()->after('lesson_id');
//        });

        // Cập nhật uuid cho tất cả bài học hiện tại chưa có uuid
        $lessons = \App\Models\Lesson::whereNull('uuid')->get();

        foreach ($lessons as $lesson) {
            $lesson->uuid = (string) Str::uuid();
            $lesson->save();
        }
    }

    public function down()
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
}
