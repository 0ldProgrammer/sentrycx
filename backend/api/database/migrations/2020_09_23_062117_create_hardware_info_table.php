<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHardwareInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hardware_info', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('redflag_id')->nullable();
            $table->integer('worker_id')->nullable();
            $table->text('station_number')->nullable();
            $table->text('gpu')->nullable();
            $table->text('disk_drive')->nullable();
            $table->text('processor')->nullable();
            $table->text('os')->nullable();
            $table->text('network_interface')->nullable();
            $table->text('sound_card')->nullable();
            $table->text('printer')->nullable();
            $table->text('monitor')->nullable();
            $table->text('camera')->nullable();
            $table->text('keyboard')->nullable();
            $table->text('mouse')->nullable();
            $table->text('installed_apps')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hardware_info');
    }
}
