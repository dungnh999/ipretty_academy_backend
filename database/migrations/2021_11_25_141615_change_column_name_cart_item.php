<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnNameCartItem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cart_item', function (Blueprint $table) {
            $table->dropForeign(['cart_item']);
        });

        Schema::table('cart_item', function (Blueprint $table) {
            $table->renameColumn('cart_item', 'cart_id');
        });

        Schema::table('cart', function (Blueprint $table) {
            $table->string('cart_token')->nullable()->change();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cart_item', function (Blueprint $table) {
            $table->renameColumn('cart_id', 'cart_item');
            $table->foreign('cart_item')->references('id')->on('cart');
        });

        Schema::table('cart', function (Blueprint $table) {
            $table->dropTimestamps();
            $table->string('cart_token')->change();
        });

    }
}
