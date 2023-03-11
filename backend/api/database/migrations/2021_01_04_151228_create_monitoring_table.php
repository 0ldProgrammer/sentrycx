<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMonitoringTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monitoring', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('workstation_id')->nullable();
            $table->integer('worker_id')->nullable();
            $table->string('application')->nullable();
            $table->text('ping_ref')->nullable();
            $table->text('ping')->nullable();
            $table->text('traceroute_ref')->nullable();
            $table->text('traceroute')->nullable();
            $table->text('mtr_ref')->nullable();
            $table->text('mtr')->nullable();
            $table->string('type')->nullable()->default('AUTO');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('monitoring');
    }
}
