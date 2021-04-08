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
            $table->integer('master_task_id');
            $table->integer('task_type');
            $table->text('file_path');
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
