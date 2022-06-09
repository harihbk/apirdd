<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTblProjectTemplateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_project_template', function (Blueprint $table) {
            $table->string('mom_status')->default(0)->comment('0-notsent,1-sent');

            //
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_project_template', function (Blueprint $table) {
            $table->string('mom_status')->default(0)->comment('0-notsent,1-sent');

            //
        });
    }
}
