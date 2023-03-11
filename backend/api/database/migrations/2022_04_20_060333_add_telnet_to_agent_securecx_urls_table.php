<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTelnetToAgentSecurecxUrlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('agent_securecx_urls', function (Blueprint $table) {
            $table->string('gate_1_telnet_80', 10)->nullable();
            $table->string('gate_1_telnet_443', 10)->nullable();
            $table->string('gate_2_telnet_80', 10)->nullable();
            $table->string('gate_2_telnet_443', 10)->nullable();
            $table->string('gate_3_telnet_80', 10)->nullable();
            $table->string('gate_3_telnet_443', 10)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('agent_securecx_urls', function (Blueprint $table) {
            //
        });
    }
}
