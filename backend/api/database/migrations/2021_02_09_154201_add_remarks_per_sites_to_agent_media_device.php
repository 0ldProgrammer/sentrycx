<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRemarksPerSitesToAgentMediaDevice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('agent_media_device', function (Blueprint $table) {
            $table -> text('remarks_per_sites')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('agent_media_device', function (Blueprint $table) {
            $table -> removeColumn('remarks_per_sites');
        });
    }
}
