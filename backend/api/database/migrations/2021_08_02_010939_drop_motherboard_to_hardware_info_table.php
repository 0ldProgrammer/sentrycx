<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropMotherboardToHardwareInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hardware_info', function (Blueprint $table) {
            $table->dropColumn('mother_board');
        });
    
        Schema::table('hardware_info', function (Blueprint $table) {
            $table -> text('mother_board')->nullable();
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
            $table -> text('mother_board')->nullable();
        });
    }
}
