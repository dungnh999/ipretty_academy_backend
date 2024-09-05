<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsCompletedToCoursesStudents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('courses_students', function (Blueprint $table) {
            $table->dateTime('completed_at')->nullable();
            $table->dateTime('started_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('courses_students', function (Blueprint $table) {
            $table->dropColumn(['completed_at', 'started_at']);
        });
    }
}
