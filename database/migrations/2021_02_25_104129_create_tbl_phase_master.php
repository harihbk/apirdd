<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblPhaseMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_phase_master', function (Blueprint $table) {
            $table->increments('phase_id');
            $table->integer('org_id');
            $table->text('phase_name');
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
        Schema::dropIfExists('tbl_phase_master');
    }
}
