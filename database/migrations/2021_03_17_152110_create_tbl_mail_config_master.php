<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblMailConfigMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_mail_config_master', function (Blueprint $table) {
            $table->increments('config_id');
            $table->integer('org_id');
            $table->integer('mail_driver');
            $table->text('domain');
            $table->mediumInteger('port');
            $table->text('user_name');
            $table->text('password');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            $table->integer('isDeleted')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_mail_config_master');
    }
}
