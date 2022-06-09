<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblAttendeesApprovals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_attendees_approvals', function (Blueprint $table) {
            $table->increments('approval_id');
            $table->mediumInteger('project_id');
            $table->integer('phase_id');
            $table->mediumInteger('task_id');
            $table->integer('task_type');
            $table->integer('attendee');
            $table->integer('approval_status')->default(0);
            $table->integer('task_status')->default(0);
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
        Schema::dropIfExists('tbl_attendees_approvals');
    }
}
