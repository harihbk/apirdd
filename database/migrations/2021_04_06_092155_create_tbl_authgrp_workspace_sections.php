<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblAuthgrpWorkspaceSections extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_authgrp_workspace_sections', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('org_id');
            $table->integer('group_id');
            $table->integer('content_id');
            $table->integer('display')->default(0)->nullable();
            $table->integer('change')->default(0)->nullable();
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
        Schema::dropIfExists('tbl_authgrp_workspace_sections');
    }
}
