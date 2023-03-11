<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSpeedtestSourceToLogsSpeedtestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('logs_speedtest', function (Blueprint $table) {
            $table->string('speedtest_source', 20);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('logs_speedtest', function (Blueprint $table) {
            $table->dropColumn('speedtest_source');
        });
    }
}
