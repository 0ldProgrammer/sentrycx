<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTriggerLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trigger_logs', function (Blueprint $table) {
            $table->id();
            $table->string('worker_id');
            $table->string('username');
            $table->string('workstation_name');
            $table->string('triggered_event');
            $table->dateTime('date_triggered');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trigger_logs');
    }
}
