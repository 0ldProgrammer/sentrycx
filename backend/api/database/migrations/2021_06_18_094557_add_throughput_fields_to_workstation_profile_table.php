<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddThroughputFieldsToWorkstationProfileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('workstation_profile', function (Blueprint $table) {
            $table -> text('Current_lan_speed')->nullable();
            $table -> text('Theoretical_Throughput')->nullable();
            $table -> text('Maximum_Possible_Throughput')->nullable();
            $table -> text('Throughput_percentage')->nullable();
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
            $table -> removeColumn('Current_lan_speed');
            $table -> removeColumn('Theoretical_Throughput');
            $table -> removeColumn('Maximum_Possible_Throughput');
            $table -> removeColumn('Throughput_percentage');
        });
    }
}
