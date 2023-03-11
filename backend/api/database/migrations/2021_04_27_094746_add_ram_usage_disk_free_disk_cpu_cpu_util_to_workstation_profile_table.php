<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRamUsageDiskFreeDiskCpuCpuUtilToWorkstationProfileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('workstation_profile', function (Blueprint $table) {
            $table -> string('ram_usage');
            $table -> string('disk');
            $table -> string('free_disk');
            $table -> string('cpu');
            $table -> string('cpu_util');
            $table -> string('desktop_app_version');
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
            $table -> removeColumn('ram_usage');
            $table -> removeColumn('disk');
            $table -> removeColumn('free_disk');
            $table -> removeColumn('cpu');
            $table -> removeColumn('cpu_util');
            $table -> removeColumn('desktop_app_version');
        });
    }
}
