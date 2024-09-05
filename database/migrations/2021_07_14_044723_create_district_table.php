<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDistrictTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        if (!Schema::hasTable('district')) {
            Schema::create('district', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name')->nullable();
                $table->string('prefix')->nullable();
                $table->integer('province_id')->nullable();
            });
        }
      
    }

    /**cls
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('district');
    }
}
