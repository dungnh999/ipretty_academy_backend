<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveForeignCmt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('comments_faq', function (Blueprint $table) {
            // $table->integer('faq_id')->nullable()->change();
            $table->dropForeign(['faq_id']);
            $table->dropColumn(['faq_id']);
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('comments_faq', function (Blueprint $table) {
            $table->integer('faq_id')->unsigned();
        });
    }
}
