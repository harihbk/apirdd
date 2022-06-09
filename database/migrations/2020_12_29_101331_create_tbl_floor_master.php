<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblFloorMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_floor_master', function (Blueprint $table) {
            $table->bigIncrements('floor_id');
            $table->mediumInteger('org_id');
            $table->bigInteger('property_id');
            $table->integer('floor_no');
            $table->string('floor_code',5);
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
        Schema::dropIfExists('tbl_floor_master');
    }
}
