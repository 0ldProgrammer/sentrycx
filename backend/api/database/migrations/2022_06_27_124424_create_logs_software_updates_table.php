<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogsSoftwareUpdatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logs_software_updates', function (Blueprint $table) {
            $table->id();
            $table->integer('worker_id');
            $table->string('update_id')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_installed')->nullable();
            $table->text('patch_name')->nullable();
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
        Schema::dropIfExists('logs_software_updates');
    }
}
