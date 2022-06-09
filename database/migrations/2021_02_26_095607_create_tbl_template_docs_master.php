<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblTemplateDocsMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_template_docs_master', function (Blueprint $table) {
            $table->increments('doc_id');
            $table->integer('org_id');
            $table->integer('project_id');
            $table->integer('phase_id');
            $table->text('doc_header');
            $table->text('doc_title');
            $table->text('reviewers');
            $table->text('approvers_level1');
            $table->text('approvers_level2');
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
        Schema::dropIfExists('tbl_template_docs_master');
    }
}
