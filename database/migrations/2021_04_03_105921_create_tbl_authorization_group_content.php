<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblAuthorizationGroupContent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_authorization_group_content', function (Blueprint $table) {
            $table->increments('id');
            $table->mediumInteger('org_id');
            $table->mediumInteger('group_id');
            $table->mediumInteger('phase_id');
            $table->mediumInteger('content_id');
            $table->mediumInteger('content_description');
            $table->integer('project_display')->default(0)->nullable();
            $table->integer('project_edit')->default(0)->nullable();
            $table->integer('template_display')->default(0)->nullable();
            $table->integer('template_edit')->default(0)->nullable();
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
        Schema::dropIfExists('tbl_authorization_group_content');
    }
}
