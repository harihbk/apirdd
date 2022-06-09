<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMemberLevelMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_member_level_master', function (Blueprint $table) {
            $table->bigIncrements('level_id');
            $table->mediumInteger('org_id');
            $table->text('level_name');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            $table->integer('created_by');
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
        Schema::dropIfExists('member_level_master');
    }
}
