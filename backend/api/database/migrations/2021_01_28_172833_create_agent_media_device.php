<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgentMediaDevice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agent_media_device', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('worker_id')->nullable();
            $table->boolean('audio')->default(false);
            $table->boolean('mic')->default(false);
            $table->boolean('video')->default(false);
            $table->text('remarks')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agent_media_device');
    }
}
