<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('order_id');
            $table->bigInteger('user_id')->unsigned();
            $table->enum('status', ['ordered', 'checkedout', 'paid', 'canceled'])->default('ordered');
            $table->bigInteger('total')->unsigned()->nullable()->default(0);
            $table->bigInteger('grandTotal')->unsigned()->nullable()->default(0);
            $table->bigInteger('salePrice')->unsigned()->nullable()->default(0);
            $table->string('discount_code')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('orders');
    }
}
