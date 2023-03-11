<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConnectedAgentsLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agent_connections_log', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table -> string('agent_name')->nullable();
            $table -> string('agent_email')->nullable();
            $table -> integer('worker_id')->nullable();
            $table -> string('station_name')->nullable();
            $table -> string('location')->nullable();
            $table -> string('account')->nullable();
            $table -> string('country')->nullable();
            $table -> string('connection_type')->nullable();
            $table -> string('net_type')->nullable();
            $table -> string('job_profile')->nullable();
            $table -> string('lob')->nullable();
            $table -> string('msa_client')->nullable();
            $table -> string('programme_msa')->nullable();
            $table -> string('supervisor_email_id')->nullable();
            $table -> string('supervisor_full_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('connected_agents_log');
    }
}
