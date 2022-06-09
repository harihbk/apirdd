<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblProjectInspections extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_project_inspections', function (Blueprint $table) {
            $table->increments('inspection_id');
            $table->integer('project_id');
            $table->integer('phase_id');
            $table->text('inspection_type');
            $table->dateTime('requested_time');
            $table->integer('checklist_id');
            $table->text('checklist_file_path');
            $table->text('comments');
            $table->integer('inspection_status')->default(0);
            $table->integer('investor_id');
            $table->integer('action_by')->default(0);
            $table->integer('isReport_generated')->default(0);
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
        Schema::dropIfExists('tbl_project_inspections');
    }
}
