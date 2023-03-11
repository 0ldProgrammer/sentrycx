<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColorAuxStatusToAuxListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('aux_list', function (Blueprint $table) {
            $table -> string('color', 50)->nullable();
            $table -> string('aux_status', 100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('aux_list', function (Blueprint $table) {
            $table -> removeColumn('color');
            $table -> removeColumn('aux_status');
        });
    }
}
