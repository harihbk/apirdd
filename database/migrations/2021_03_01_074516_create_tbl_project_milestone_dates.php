<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblProjectMilestoneDates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_project_milestone_dates', function (Blueprint $table) {
            $table->increments('date_id');
            $table->integer('org_id');
            $table->integer('project_id');
            $table->dateTime('concept_submission');
            $table->dateTime('detailed_design_submission');
            $table->dateTime('unit_handover');
            $table->dateTime('fitout_start');
            $table->dateTime('fitout_completion');
            $table->dateTime('store_opening');
            $table->integer('active_status')->default(0);
            $table->integer('created_by');
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
        Schema::dropIfExists('tbl_project_milestone_dates');
    }
}
