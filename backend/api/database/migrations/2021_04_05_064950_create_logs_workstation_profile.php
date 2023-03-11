<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogsWorkstationProfile extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logs_workstation_profile', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('worker_id')->nullable();
            $table->string('host_name')->nullable();
            $table->string('host_ip_address')->nullable();
            $table->string('subnet')->nullable();
            $table->string('gateway')->nullable();
            $table->string('VLAN')->nullable();
            $table->string('DNS_1')->nullable();
            $table->string('DNS_2')->nullable();
            $table->string('station_number')->nullable();
            $table->text('ISP')->nullable();
            $table->text('download_speed')->nullable();
            $table->text('upload_speed')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('logs_workstation_profile');
    }
}
