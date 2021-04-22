<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblProjectWorkpermits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_project_workpermits', function (Blueprint $table) {
            $table->increments('permit_id');
            $table->integer('project_id');
            $table->integer('phase_id');
            $table->integer('work_permit_type');
            $table->integer('file_path')->nullable();
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->text('description')->nullable();
            $table->text('checklist_file_path')->nullable();
            $table->integer('request_status')->default(0);
            $table->integer('investor_id')->default(0);
            $table->integer('rdd_member_id')->default(0);
            $table->integer('action_by')->default(0);
            $table->integer('isDeleted')->default(0);
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
        Schema::dropIfExists('tbl_project_workpermits');
    }
}
