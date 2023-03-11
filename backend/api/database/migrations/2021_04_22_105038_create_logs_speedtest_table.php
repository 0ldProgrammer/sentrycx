<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogsSpeedtestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logs_speedtest', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('worker_id')->nullable();
            $table->string('connection_type');
            $table->string('download_speed');
            $table->string('upload_speed');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('logs_speedtest');
    }
}
