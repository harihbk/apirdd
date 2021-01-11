<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblMailfrequencyMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_mailfrequency_master', function (Blueprint $table) {
            $table->increments('fre_id');
            $table->mediumInteger('org_id');
            $table->integer('notification_frequency');
            $table->integer('interval_days');
            $table->integer('esc_level');
            $table->integer('due_date_percentage');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            $table->integer('created_by');
            $table->integer('active_status')->default(1);
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
        Schema::dropIfExists('tbl_mailfrequency_master');
    }
}
