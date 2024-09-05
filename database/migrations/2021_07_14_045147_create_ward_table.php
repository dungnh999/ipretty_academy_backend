<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWardTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('ward')) {
            Schema::create('ward', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name')->nullable();
                $table->string('prefix')->nullable();
                $table->integer('province_id')->nullable();
                $table->integer('district_id')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ward');
    }
}
