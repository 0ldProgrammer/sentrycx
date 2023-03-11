<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCnxLanIdToCnxEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cnx_employees', function (Blueprint $table) {
            $table -> text('cnx_lan_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cnx_employees', function (Blueprint $table) {
            $table -> removeColumn('cnx_lan_id');
        });
    }
}
