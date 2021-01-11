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
            $table->integer('leasing_representative');
            $table->text('leasing_comments');
            $table->integer('fitout_period');
            $table->integer('fitout_deposit_status');
            $table->double('fitout_deposit_amt', 5, 4)->nullable();
            $table->integer('fitout_currency_type');
            $table->date('insurance_validity_date');
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
