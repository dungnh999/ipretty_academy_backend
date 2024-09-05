<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveFiledInFrequentlyAskedQuestion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('frequently_asked_questions', function (Blueprint $table) {
            if (Schema::hasColumn('frequently_asked_questions', 'type_category_id')) {
                $table->dropForeign(['type_category_id']);
                $table->dropColumn('type_category_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('frequently_asked_questions', function (Blueprint $table) {
            $table->integer('type_category_id')->unsigned()->nullable();
            $table->foreign('type_category_id')->references('id')->on('faq_category');
        });
    }
}
