<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblProjectTemplate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_project_template', function (Blueprint $table) {
            $table->increments('id');
            $table->mediumInteger('org_id');
            $table->mediumInteger('project_id');
            $table->mediumInteger('template_id')->default(0);
            $table->mediumInteger('template_master_id')->default(0);
            $table->integer('task_id');
            $table->integer('task_type');
            $table->text('activity_desc');
            $table->date('meeting_date')->nullable();
            $table->time('meeting_start_time')->nullable();
            $table->time('meeting_end_time')->nullable();
            $table->text('approvers')->nullable();
            $table->text('approvers_designation')->nullable();
            $table->text('attendees')->nullable();
            $table->text('attendees_designation')->nullable();
            $table->integer('phase_id');
            $table->text('mem_responsible');
            $table->text('mem_responsible_designation')->nullable();
            $table->integer('fre_id');
            $table->integer('duration');
            $table->integer('priority');
            $table->integer('seq_status')->default(0);
            $table->integer('seq_no')->nullable();
            $table->dateTime('planned_date');
            $table->dateTime('actual_date');
            $table->dateTime('fif_upload_path');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            $table->integer('created_by');
            $table->integer('isProjecttask')->default(0);
            $table->integer('isForwarded')->default(0);
            $table->integer('task_status')->default(0);
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
        Schema::dropIfExists('tbl_project_template');
    }
}
