<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIspFullnameToWorkstationProfileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('workstation_profile', function (Blueprint $table) {
            $table -> string('isp_fullname');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('workstation_profile', function (Blueprint $table) {
            $table -> removeColumn('isp_fullname');
        });
    }
}
