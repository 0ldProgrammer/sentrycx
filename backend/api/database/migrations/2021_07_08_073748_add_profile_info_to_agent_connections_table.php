<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProfileInfoToAgentConnectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('agent_connections', function (Blueprint $table) {
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
        Schema::table('agent_connections', function (Blueprint $table) {
            $table -> removeColumn('job_profile');
            $table -> removeColumn('lob');
            $table -> removeColumn('msa_client');
            $table -> removeColumn('programme_msa');
            $table -> removeColumn('supervisor_email_id');
            $table -> removeColumn('supervisor_full_name');
        });
    }
}
