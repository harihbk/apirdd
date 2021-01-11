<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnitsMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_units_master', function (Blueprint $table) {
            $table->bigIncrements('unit_id');
            $table->mediumInteger('org_id');
            $table->bigInteger('property_id');
            $table->integer('floor_no');
            $table->string('floor_code',15);
            $table->mediumInteger('unit_area');
            $table->text('pod_image_path');
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
        Schema::dropIfExists('units_master');
    }
}
