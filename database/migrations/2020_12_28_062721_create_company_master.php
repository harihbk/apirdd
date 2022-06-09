<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_company_master', function (Blueprint $table) {
            $table->bigIncrements('company_id');
            $table->mediumInteger('org_id');
            $table->text('company_name');
            $table->text('brand_name');
            $table->text('contact_person');
            $table->string('contact_email',30);
            $table->text('mobile_no');
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
        Schema::dropIfExists('company_master');
    }
}
