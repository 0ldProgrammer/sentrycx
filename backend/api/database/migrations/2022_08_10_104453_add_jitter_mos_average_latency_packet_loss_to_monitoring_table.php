<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJitterMosAverageLatencyPacketLossToMonitoringTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('monitoring', function (Blueprint $table) {
            $table -> float('average_latency', 8, 2)->nullable();
            $table -> float('packet_loss', 8, 2)->nullable();
            $table -> float('jitter', 8,2)->nullable();
            $table -> float('mos', 8, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('monitoring', function (Blueprint $table) {
            //
        });
    }
}
