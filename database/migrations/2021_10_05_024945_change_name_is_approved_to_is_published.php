<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeNameIsApprovedToIsPublished extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->renameColumn('is_approved', 'is_published');
            $table->dropForeign(['leader_id']);
            $table->dropColumn(['leader_id']);
        });
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn(['lesson_status']);
        });
        Schema::table('chapters', function (Blueprint $table) {
            $table->integer('number_order')->nullable()->default(0);
        });
        Schema::table('chapters_lessons', function (Blueprint $table) {
            $table->integer('number_order')->nullable()->default(0);
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
            $table->renameColumn('is_published', 'is_approved');
            $table->bigInteger('leader_id')->unsigned()->nullable();
            $table->foreign('leader_id')->references('id')->on('users');
        });
        Schema::table('lessons', function (Blueprint $table) {
            $table->enum('lesson_status', ['Draft', 'Publish'])->default('Publish');
        });
        Schema::table('chapters_lessons', function (Blueprint $table) {
            $table->dropColumn(['number_order']);
        });
        Schema::table('chapters', function (Blueprint $table) {
            $table->dropColumn(['number_order']);
        });
    }
}
