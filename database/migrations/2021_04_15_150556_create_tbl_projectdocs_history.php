<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblProjectdocsHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_projectdocs_history', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('org_id');
            $table->integer('project_id');
            $table->integer('doc_id');
            $table->text('file_name');
            $table->text('file_path')->nullable();
            $table->integer('uploaded_by');
            $table->integer('approval_status')->default(0);
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
        Schema::dropIfExists('tbl_projectdocs_history');
    }
}
