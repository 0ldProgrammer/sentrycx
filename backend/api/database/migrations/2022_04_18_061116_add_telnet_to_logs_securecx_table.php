<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTelnetToLogsSecurecxTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('logs_securecx', function (Blueprint $table) {
            $table->string('telnet_80', 10)->nullable();
            $table->string('telnet_443', 10)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('logs_securecx', function (Blueprint $table) {
            //
        });
    }
}
