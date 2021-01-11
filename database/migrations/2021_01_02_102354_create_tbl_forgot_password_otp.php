<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblForgotPasswordOtp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_forgot_password_otp', function (Blueprint $table) {
            $table->increments('otp_id');
            $table->mediumInteger('user_id');
            $table->string('user_email',30);
            $table->integer('user_type');
            $table->text('otp');
            $table->integer('otp_status')->default(0);
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_forgot_password_otp');
    }
}
