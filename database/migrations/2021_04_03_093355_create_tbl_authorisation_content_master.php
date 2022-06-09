<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblAuthorisationContentMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_authorisation_content_master', function (Blueprint $table) {
            $table->increments('id');
            $table->mediumInteger('org_id');
            $table->text('content');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
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
        Schema::dropIfExists('tbl_authorisation_content_master');
    }
}
