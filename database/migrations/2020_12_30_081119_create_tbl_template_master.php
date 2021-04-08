<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblTemplateMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_template_master', function (Blueprint $table) {
            $table->increments('master_id');
            $table->mediumInteger('template_id');
            $table->mediumInteger('org_id');
            $table->integer('task_id');
            $table->integer('task_type');
            $table->integer('phase_id');
            $table->text('activity_desc');
            $table->integer('person');
            $table->integer('approvers');
            $table->integer('attendees');
            $table->integer('fre_id');
            $table->integer('seq_status')->default(0);
            $table->integer('seq_no')->nullable();
            $table->integer('duration');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->text('file_upload_path');
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
        Schema::dropIfExists('tbl_template_master');
    }
}
