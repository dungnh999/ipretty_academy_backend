<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('transaction_id');
            $table->string('transaction_code');
            $table->enum('payment_method', ['at_company', 'banking', 'point'])->default('banking');
            $table->integer('order_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->enum('status', ['processing', 'approved', 'rejected'])->default('processing');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('order_id')->references('order_id')->on('orders');
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
        Schema::drop('transactions');
    }
}
