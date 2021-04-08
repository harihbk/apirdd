<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblForwardedTasks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_forwarded_tasks', function (Blueprint $table) {
            $table->id();
            $table->mediumInteger('project_id');
            $table->integer('template_master_id');
            $table->integer('task_id');
            $table->integer('task_type');
            $table->text('forwarded_from');
            $table->text('forwarded_to');
            $table->text('approvers')->nullable();
            $table->text('attendees')->nullable();
            $table->text('phase_name');
            $table->text('phase_date');
            $table->text('mem_responsible');
            $table->integer('fre_id');
            $table->integer('duration');
            $table->integer('priority');
            $table->integer('seq_status')->default(0);
            $table->integer('seq_no')->nullable();
            $table->string('seq_char','4')->nullable();
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            $table->integer('created_by');
            $table->integer('isProjecttask')->default(0);
            $table->integer('task_status')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_forwarded_tasks');
    }
}
