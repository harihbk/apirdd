<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblProjects extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    
    public function up()
    {
        Schema::create('tbl_projects', function (Blueprint $table) {
            $table->increments('project_id');
            $table->integer('org_id');
            $table->text('project_name');
            $table->integer('project_type');
            $table->integer('property_id');
            $table->integer('unit_id');
            $table->text('usage_permissions');
            $table->integer('fitout_period');
            $table->integer('fitout_deposit_status');
            $table->double('fitout_deposit_amt', 5, 4)->nullable();
            $table->text('fitout_deposit_filepath')->nullable();
            $table->integer('owner_work')->default(0);
            $table->double('owner_work_amt', 5, 4)->nullable();
            $table->text('owner_work_filepath')->nullable();
            $table->integer('kfd_drawing_status')->default(0);
            $table->integer('ivr_status')->default(0);
            $table->double('ivr_amt', 5, 4)->nullable();
            $table->text('ivr_filepath')->nullable();
            $table->dateTime('workpermit_expiry_date')->nullable();
            $table->integer('fitout_currency_type');
            $table->date('insurance_validity_date');
            $table->text('fif_upload_path');
            $table->text('assigned_rdd_members');
            $table->integer('investor_company');
            $table->integer('investor_brand');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            $table->integer('created_by');
            $table->integer('project_status')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_projects');
    }
}
