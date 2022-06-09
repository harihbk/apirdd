<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblAuthorizationGroups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_authorization_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->mediumInteger('org_id');
            $table->text('group_name');
            $table->mediumInteger('project_creation')->default(0);
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            $table->mediumInteger('created_by');
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
        Schema::dropIfExists('tbl_authorization_groups');
    }
}
