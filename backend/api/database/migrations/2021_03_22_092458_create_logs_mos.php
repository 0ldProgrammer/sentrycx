<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogsMos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logs_mos', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table -> float('average_latency', 8, 2)->nullable();
            $table -> float('packet_loss', 8, 2)->nullable();
            $table -> float('jitter', 8,2)->nullable();
            $table -> float('mos', 8, 2)->nullable();
            $table -> integer('worker_id')->nullable();
            $table -> string('shift_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('logs_mos');
    }
}
