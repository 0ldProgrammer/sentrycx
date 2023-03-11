<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgentSecurecxUrlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agent_securecx_urls', function (Blueprint $table) {
            $table->id();
            $table->integer('worker_id');
            $table->string('securecx_gate_1')->nullable();
            $table->string('securecx_gate_2')->nullable();
            $table->string('securecx_gate_3')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agent_securecx_urls');
    }
}
