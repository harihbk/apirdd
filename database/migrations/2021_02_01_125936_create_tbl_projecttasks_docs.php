<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblProjecttasksDocs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_projecttasks_docs', function (Blueprint $table) {
            $table->increments('doc_id');
            $table->integer('project_id');
            $table->integer('phase_id');
            $table->text('doc_header');
            $table->text('doc_title');
            $table->text('reviewers');
            $table->text('reviewers_designation');
            $table->text('approvers_level1');
            $table->text('approvers_level1_designation');
            $table->text('approvers_level2');
            $table->text('approvers_level2_designation');
            $table->text('file_path');
            $table->text('comment');
            $table->date('actual_date');
            $table->integer('doc_status');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            $table->integer('created_by');
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
        Schema::dropIfExists('tbl_projecttasks_docs');
    }
}
