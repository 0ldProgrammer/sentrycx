<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToLogsWorkstationProfileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('logs_workstation_profile', function (Blueprint $table) {
            $table->string('cpu',50)->nullable();
            $table->float('cpu_util',7,4)->nullable();
            $table->float('ram',10,4)->nullable();
            $table->float('ram_usage',7,4)->nullable();
            $table->float('DISK',10,4)->nullable();
            $table->float('free_disk',10,4)->nullable();
            $table->float('mtr_highest_avg',8,4)->nullable();
            $table->float('mtr_highest_loss',7,4)->nullable();
            $table->string('mtr_host',100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('logs_workstation_profile', function (Blueprint $table) {
            $table->dropColumn('cpu');
            $table->dropColumn('cpu_util');
            $table->dropColumn('ram');
            $table->dropColumn('ram_usage');
            $table->dropColumn('DISK');
            $table->dropColumn('free_disk');
            $table->dropColumn('mtr_highest_avg');
            $table->dropColumn('mtr_highest_loss');
            $table->dropColumn('mtr_host');
        });
    }
}
