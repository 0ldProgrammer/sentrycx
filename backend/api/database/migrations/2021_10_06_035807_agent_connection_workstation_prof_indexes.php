<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AgentConnectionWorkstationProfIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('agent_connections', function (Blueprint $table) {
            $table->index('worker_id');
        });

		Schema::table('workstation_profile', function (Blueprint $table) {
            $table->index('worker_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('agent_connections', function (Blueprint $table) {
			$table->dropIndex(['worker_id']);
		});

		Schema::table('workstation_profile', function (Blueprint $table) {
            $table->dropIndex(['worker_id']);
        });
    }
}
