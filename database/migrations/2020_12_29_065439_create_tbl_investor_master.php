<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblInvestorMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_tenant_master', function (Blueprint $table) {
            $table->bigIncrements('tenant_id');
            $table->mediumInteger('org_id');
            $table->integer('company_id');
            $table->text('brand_name');
            $table->text('tenant_name');
            $table->text('tenant_last_name');
            $table->string('email',45);
            $table->string('tenant_mobile',20);
            $table->text('tenant_designation');
            $table->integer('tenant_type');
            $table->text('tenant_address');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->string('password',255);
            $table->integer('tenant_gender');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            $table->integer('created_by');
            $table->integer('active_status')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_investor_master');
    }
}
