<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRamAndMemoryToHardwareInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hardware_info', function (Blueprint $table) {
            $table -> string('ram')->nullable();
            $table -> string('memory')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hardware_info', function (Blueprint $table) {
            $table -> removeColumn('ram');
            $table -> removeColumn('memory');
        });

    }
}
