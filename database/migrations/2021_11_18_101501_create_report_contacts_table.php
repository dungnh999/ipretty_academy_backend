<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportContactsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_contacts', function (Blueprint $table) {
            $table->increments('report_id');
            $table->string('reporter_email')->nullable();
            $table->string('reporter_phone')->nullable();
            $table->string('reporter_name')->nullable();
            $table->longText('report_content');
            $table->longText('attachments')->nullable();
            $table->bigInteger('reporter_id')->unsigned()->nullable();
            $table->boolean('isReport')->default(1);
            $table->boolean('isSended')->default(false);
            $table->timestamps();
            $table->foreign('reporter_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('report_contacts');
    }
}
