<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameAndChangeCertificateId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            if (Schema::hasColumn('courses', 'certificate_id')) {
                $table->dropForeign(['certificate_id']);
                $table->dropColumn(['certificate_id']);
            }
            $table->string('certificate_image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['certificate_image']);
            $table->integer('certificate_id')->unsigned()->nullable();
            $table->foreign('certificate_id')->references('certificate_id')->on('certificates');
        });
    }
}
