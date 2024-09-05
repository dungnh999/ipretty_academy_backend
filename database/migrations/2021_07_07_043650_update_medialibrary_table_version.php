<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateMedialibraryTableVersion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('media', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
        Schema::table('media', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->unique();
            $table->string('conversions_disk')->nullable()->change();
            $table->json('generated_conversions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('media', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
        Schema::table('media', function (Blueprint $table) {
            $table->unsignedBigInteger('uuid');
            $table->dropColumn('generated_conversions');
            $table->string('conversions_disk')->nullable(false)->change();
        });
    }
}
