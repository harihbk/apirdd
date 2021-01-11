<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTblRddMembers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_rdd_members', function($table) {
            $table->text('mem_last_name')->after('mem_name');
            $table->text('mobile_no')->after('mem_password');
            $table->integer('gender')->after('mobile_no');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_rdd_members', function($table) {
            $table->dropColumn('mem_last_name');
            $table->dropColumn('mobile_no');
            $table->dropColumn('gender');
        });
    
    }
}
