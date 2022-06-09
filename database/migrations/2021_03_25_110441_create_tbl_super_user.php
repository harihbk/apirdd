<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblSuperUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_super_users', function (Blueprint $table) {
            $table->increments('id');
            $table->text('mem_name');
            $table->string('email',50);
            $table->text('password');
            $table->text('access_token')->nullable();
            $table->text('refresh_token')->nullable();
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            $table->integer('access_type');
            $table->integer('active_status')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_super_user');
    }
}
