<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgentConnections extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agent_connections', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('agent_name')->nullable();
            $table->string('agent_email')->nullable();
            $table->integer('worker_id')->nullable();
            $table->string('station_name')->nullable();
            $table->string('location')->nullable();
            $table->string('account')->nullable();
            $table->string('country')->nullable();
            $table->string('mtr_host')->nullable();
            $table->string('mtr_highest_avg')->nullable();
            $table->string('mtr_highest_loss')->nullable();
            $table->text('mtr_result')->nullable();
            $table->string('session_id')->nullable();
            $table->boolean('is_active')->nullable();
            $table->boolean('processing')->nullable();
            $table->string('network_adapter')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agent_connections');
    }
}
