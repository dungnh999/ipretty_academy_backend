<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameAndCreateSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('sessions')) {
            Schema::rename('sessions', 'browser_sessions');
        }
        Schema::create('sessions', function (Blueprint $table) {
            $table->increments('session_id');
            $table->text('session_name');
            $table->integer('chapter_id')->unsigned()->nullable();
            $table->softDeletes();
            $table->foreign('chapter_id')->references('chapter_id')->on('chapters');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('sessions');

        if (Schema::hasTable('browser_sessions')) {
            Schema::rename('browser_sessions', 'sessions');
        }
    }
}
