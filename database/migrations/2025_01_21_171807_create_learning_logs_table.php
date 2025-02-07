<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLearningLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('learning_logs', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->unsignedBigInteger('user_id'); // Foreign key for users
            $table->unsignedBigInteger('course_id'); // Foreign key for courses
            $table->unsignedBigInteger('track_id'); // Foreign key for tracks
            $table->unsignedBigInteger('track_step_id'); // Foreign key for track steps

            $table->boolean('is_completed')->default(false); // Whether the step is completed
            $table->timestamp('start_time')->nullable(); // Time when the step was started
            $table->timestamp('end_time')->nullable(); // Time when the step was completed

            $table->timestamps(); // Created at and updated at columns
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('learning_logs');
    }
}
