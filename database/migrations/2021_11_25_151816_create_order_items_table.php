<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderItemsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->increments('order_item_id');
            $table->integer('course_id')->unsigned();
            $table->integer('order_id')->unsigned();
            $table->bigInteger('course_price')->unsigned()->default(0);
            $table->timestamps();
            $table->foreign('course_id')->references('course_id')->on('courses');
            $table->foreign('order_id')->references('order_id')->on('orders');  
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('order_items');
    }
}
