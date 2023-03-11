<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProgressResults extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	if (Schema::hasTable('progress_results') )
		return;

        Schema::create('progress_results', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('worker_id')->nullable();
            $table->double('progress');
            $table->smallInteger('type')->comment('1 for MTR_REQUEST, 2 for WORKSTATION_PROFILE');
            $table->date('last_progress');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('progress_results');
    }
}
