<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMonitoringDiskDriveTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monitoring_disk_drive', function (Blueprint $table) {
            $table->bigInteger('id')->unsigned()->autoIncrement();
            $table->integer('worker_id');
            $table->integer('monitoring_id')->nullable();
            $table->string('name', 5)->nullable();
            $table->string('type', 15);
            $table->string('volume_label', 32)->nullable();
            $table->string('file_system', 20)->nullable();
            $table->double('available_free_space', 8, 4);
            $table->double('total_free_space', 8, 4);
            $table->double('total_size', 8, 4);
            $table->string('root_directory', 5)->nullable();
            $table->string('system_drive', 100)->nullable();
            $table->timestamps();
            $table->index(['worker_id', 'monitoring_id']);
            $table->index('monitoring_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('monitoring_disk_drive');
    }
}
