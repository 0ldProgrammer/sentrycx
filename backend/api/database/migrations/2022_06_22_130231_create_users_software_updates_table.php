<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersSoftwareUpdatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_software_updates', function (Blueprint $table) {
            $table->id();
            $table->integer('worker_id');
            $table->string('os')->nullable();
            $table->string('update_id')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_installed')->nullable();
            $table->string('support_url')->nullable();
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
        Schema::dropIfExists('users_software_updates');
    }
}
