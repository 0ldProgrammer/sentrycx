<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LogsWorkstationTrace extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logs_workstation_trace', function (Blueprint $table) {
            $table -> id();
            $table -> timestamps();
            $table -> integer('worker_id')->nullable();
            $table -> string('workstation')->nullable();
            $table -> text('message')->nullable();
            $table -> text('stacktrace')->nullable();
            $table -> dateTime('timelog')->nullable();
            $table -> string('type')->default('ERROR');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
