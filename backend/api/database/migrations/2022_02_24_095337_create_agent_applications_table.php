<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgentApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agent_applications', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('worker_id');
            $table->integer('application_id')->nullable();
            $table->string('type', 50)->nullable();
            $table->string('account')->nullable();
            $table->string('location')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agent_applications');
    }
}
