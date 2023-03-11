<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateThresholdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('thresholds', function (Blueprint $table) {
            $table->id();
            $table->integer('account_id');
            $table->float('latency',8,2)->nullable();
            $table->float('average_latency',8,2)->nullable();
            $table->float('packet_loss',8,2)->nullable();
            $table->float('download_speed',8,2)->nullable();
            $table->float('upload_speed',8,2)->nullable();
            $table->float('jitter',8,2)->nullable();
            $table->float('mos',8,2)->nullable();
            $table->float('ram',8,2)->nullable();
            $table->float('ram_usage',8,2)->nullable();
            $table->float('disk',8,2)->nullable();
            $table->float('free_disk',8,2)->nullable();
            $table->float('cpu_utilization',8,2)->nullable();
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
        Schema::dropIfExists('thresholds');
    }
}
