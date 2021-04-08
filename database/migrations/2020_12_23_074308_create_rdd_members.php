<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRddMembers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('mem_id');
            $table->mediumInteger('mem_org_id');
            $table->text('mem_name');
            $table->string('email',30);
            $table->string('password',255);
            $table->integer('mem_designation');
            $table->text('mem_signature_path');
            $table->integer('mem_level');
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
        Schema::dropIfExists('rdd_members');
    }
}
