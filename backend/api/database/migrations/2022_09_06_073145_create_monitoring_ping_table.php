<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMonitoringPingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monitoring_ping', function (Blueprint $table) {
            $table->bigInteger('id')->unsigned()->autoIncrement();
            $table->integer('worker_id');
            $table->integer('monitoring_id')->nullable();
            $table->boolean('is_reference');
            $table->boolean('is_mos');
            $table->tinyInteger('roundtrip_sequence')->unsigned();
            $table->string('hostname')->nullable();
            $table->string('address', 15)->nullable();
            $table->string('reply_status', 30)->nullable();
            $table->string('reply_address', 15)->nullable();
            $table->smallInteger('reply_buffer')->unsigned()->nullable();
            $table->smallInteger('reply_roundtrip_time')->unsigned()->nullable();
            $table->tinyInteger('reply_ttl')->unsigned()->nullable();
            $table->timestamps();
            $table->index(['worker_id', 'monitoring_id', 'is_reference', 'is_mos', 'roundtrip_sequence'], 'ping_composite_index');
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
        Schema::dropIfExists('monitoring_ping');
    }
}
