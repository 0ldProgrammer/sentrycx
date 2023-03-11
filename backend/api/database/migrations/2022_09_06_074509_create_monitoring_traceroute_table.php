<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMonitoringTracerouteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monitoring_traceroute', function (Blueprint $table) {
            $table->bigInteger('id')->unsigned()->autoIncrement();
            $table->integer('worker_id');
            $table->integer('monitoring_id')->nullable();
            $table->boolean('is_reference');
            $table->boolean('is_mtr');
            $table->string('hostname');
            $table->string('address', 15)->nullable();
            $table->tinyInteger('hop_sequence')->unsigned();
            $table->tinyInteger('roundtrip_sequence')->unsigned();
            $table->string('reply_hostname')->nullable();
            $table->string('reply_address', 15)->nullable();
            $table->smallInteger('reply_roundtrip_time')->unsigned()->nullable();
            $table->timestamps();
            $table->index(['worker_id', 'monitoring_id', 'is_reference', 'is_mtr', 'hop_sequence', 'roundtrip_sequence'], 'traceroute_composite_index');
            $table->index('monitoring_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('monitoring_traceroute');
    }
}
