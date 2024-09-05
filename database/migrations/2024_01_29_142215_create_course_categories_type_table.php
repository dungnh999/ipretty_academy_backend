<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseCategoriesTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_categories_types', function (Blueprint $table) {
          $table->increments('category_type_id');
          $table->string('category_type_name');
          $table->longText('category_type_description')->nullable();
          $table->bigInteger('created_by')->unsigned()->nullable();
          $table->timestamps();
          $table->softDeletes();
          $table->boolean('isPublished')->default(false);
          $table->foreign('created_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_categories_types');
    }
}
