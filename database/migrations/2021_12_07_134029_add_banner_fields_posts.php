<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBannerFieldsPosts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('introduction')->nullable();
            $table->string('sub_introduction')->nullable();
            $table->string('color_introduction')->nullable();
            $table->string('color_title')->nullable();
            $table->string('color_content')->nullable();
            $table->string('bg_color_button')->nullable();
            $table->string('color_button')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['introduction', 'sub_introduction', 'color_introduction', 'color_title', 'color_content', 'color_button', 'bg_color_button']);
        });
    }
}
