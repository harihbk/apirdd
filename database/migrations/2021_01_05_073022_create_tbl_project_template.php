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
            $table->mediumInteger('project_id');
            $table->mediumInteger('template_id')->default(0);
            $table->mediumInteger('template_master_id')->default(0);
            $table->integer('task_id');
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
