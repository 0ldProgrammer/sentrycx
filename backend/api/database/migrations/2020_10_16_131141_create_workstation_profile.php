<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkstationProfile extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('workstation_profile'))
            return;


        Schema::create('workstation_profile', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('redflag_id');
            $table->integer('worker_id')->nullable();
            $table->string('host_name')->nullable();
            $table->string('host_ip_address')->nullable();
            $table->string('subnet')->nullable();
            $table->string('gateway')->nullable();
            $table->string('VLAN')->nullable();
            $table->string('DNS_1')->nullable();
            $table->string('DNS_2')->nullable();
            $table->string('station_number')->nullable();
            $table->string('selected_ip')->nullable();
            $table->string('selected_host')->nullable();
            $table->text('ping')->nullable();
            $table->text('ping_ref')->nullable();
            $table->text('tracecert')->nullable();
            $table->text('tracecert_ref')->nullable();
            $table->text('host_file')->nullable();
            $table->text('ISP')->nullable();
            $table->text('download_speed')->nullable();
            $table->text('upload_speed')->nullable();
            $table->text('mtr')->nullable();
            $table->text('network_adapter')->nullable();
            $table->timestamp('date_created')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('workstation_profile');
    }
}
