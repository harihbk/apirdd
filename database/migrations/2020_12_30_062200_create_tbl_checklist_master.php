<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblChecklistMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_checklist_master', function (Blueprint $table) {
            $table->increments('ch_id');
            $table->mediumInteger('org_id');
            $table->mediumInteger('template_id');
            $table->integer('template_id');
            $table->integer('root_id');
            $table->text('checklist_desc');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            $table->integer('created_by');
            $table->integer('active_status')->default(1);
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
        Schema::dropIfExists('tbl_checklist_master');
    }
}
