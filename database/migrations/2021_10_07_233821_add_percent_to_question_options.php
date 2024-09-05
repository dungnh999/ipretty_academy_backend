<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPercentToQuestionOptions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->integer('percent_achieved')->nullable()->default(0);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->json('meta')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn(['percent_achieved']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('meta')->nullable()->change();
        });
    }
}
