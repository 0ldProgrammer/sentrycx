<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('logs_mos', function (Blueprint $table) {
            $table->integer('worker_id')->nullable()->change();
        });

        Schema::table('logs_speedtest', function (Blueprint $table) {
            $table->integer('worker_id')->nullable()->change();
        });

        Schema::table('logs_workstation_profile', function (Blueprint $table) {
            $table->integer('worker_id')->nullable()->change();
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->integer('worker_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('logs_mos', function (Blueprint $table) {
            $table->integer('worker_id')->change();
        });

        Schema::table('logs_speedtest', function (Blueprint $table) {
            $table->integer('worker_id')->change();
        });

        Schema::table('logs_workstation_profile', function (Blueprint $table) {
            $table->integer('worker_id')->change();
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->integer('worker_id')->change();
        });
    }
}
