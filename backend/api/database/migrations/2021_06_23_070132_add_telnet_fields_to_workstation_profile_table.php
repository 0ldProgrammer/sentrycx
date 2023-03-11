<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTelnetFieldsToWorkstationProfileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('workstation_profile', function (Blueprint $table) {
            //
            $table -> text('Telnet80')->nullable();
            $table -> text('Telnet443')->nullable();
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
            //
            $table -> removeColumn('Telnet80');
            $table -> removeColumn('Telnet443');
        });
    }
}
