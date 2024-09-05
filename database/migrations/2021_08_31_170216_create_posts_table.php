<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->increments('post_id');
            $table->string('title');
            $table->longText('content')->nullable();
            // $table->enum('post_type', ['Banner', 'Article', 'Link', 'Introduction', 'QnA', 'News', 'Guide', 'Policy', 'Terms'])->default('Article');
            $table->longText('bannerUrl')->nullable();
            $table->string('external_url')->nullable();
            $table->string('slug')->nullable();
            $table->bigInteger('created_by')->unsigned()->nullable();
            $table->foreign('created_by')->references('id')->on('users');
            $table->integer('category_id')->unsigned()->nullable();
            $table->foreign('category_id')->references('category_id')->on('post_categories');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}
