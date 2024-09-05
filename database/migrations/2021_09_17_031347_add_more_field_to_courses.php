<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMoreFieldToCourses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dateTime("deadline")->nullable();
            $table->enum('course_type', ['Local', 'Group', 'Business'])->default('Local');
            $table->boolean('is_approved')->default(false);
            $table->bigInteger('leader_id')->unsigned()->nullable();
            $table->dateTime('startTime')->nullable();
            $table->dateTime('endTime')->nullable();
            $table->decimal('course_sale_price')->nullable()->default(0);
            $table->longText('course_target')->nullable();
            $table->foreign('leader_id')->references('id')->on('users');
            $table->dropColumn(['course_status', 'course_evaluate', 'course_duration']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropForeign(['leader_id']);
            $table->dropColumn(['deadline', 'course_type', 'leader_id', 'startTime', 'endTime', 'course_sale_price', 'course_target', 'is_approved']);
            $table->double('course_duration')->nullable()->default(0);
            $table->longText('course_evaluate')->nullable();
            $table->enum('course_status', ['Open', 'Close'])->default('Close');
        });
    }
}
