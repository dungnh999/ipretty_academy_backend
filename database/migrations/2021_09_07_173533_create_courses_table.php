<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->increments('course_id');
            $table->string('course_name');
            $table->bigInteger('course_created_by')->unsigned()->nullable();
            $table->bigInteger('teacher_id')->unsigned()->nullable();
            $table->longText('course_feature_image');
            $table->longText('course_description')->nullable();
            $table->integer('count_viewer')->nullable()->default(0);
            $table->integer('category_id')->unsigned()->nullable();
            $table->enum('course_status', ['Open', 'Close'])->default('Close');
            $table->integer('course_version')->nullable()->default(1);
            $table->decimal('course_price')->nullable()->default(0);
            $table->integer('certificate_id')->unsigned()->nullable();
            $table->double('course_duration')->nullable()->default(0);
            $table->longText('course_evaluate')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('course_created_by')->references('id')->on('users');
            $table->foreign('teacher_id')->references('id')->on('users');
            $table->foreign('category_id')->references('category_id')->on('course_categories');
            $table->foreign('certificate_id')->references('certificate_id')->on('certificates');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('courses');
    }
}
