<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblAuthorizationGroupMilestones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_authorization_group_milestones', function (Blueprint $table) {
            $table->increments('id');
            $table->mediumInteger('org_id');
            $table->mediumInteger('group_id');
            $table->mediumInteger('config_id');
            $table->integer('edit')->default(0)->nullable();
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
        Schema::dropIfExists('tbl_authorization_group_milestones');
    }
}
