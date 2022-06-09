<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblOrgMilestoneConfig extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_org_milestone_config', function (Blueprint $table) {
            $table->increments('id');
            $table->mediumInteger('org_id');
            $table->mediumInteger('milestone_config_id');
            $table->text('description');
            $table->integer('display_status')->default(0);
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
        Schema::dropIfExists('tbl_org_milestone_config');
    }
}
