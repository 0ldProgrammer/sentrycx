<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePotentialTriggersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('potential_triggers', function (Blueprint $table) {
            $table->id();
            $table->string('event');
            $table->string('triggered_by');
            $table->dateTime('datetime_triggered');
            $table->string('agent_name');
            $table->integer('worker_id')->nullable();
            $table->string('email')->nullable(true);
            $table->string('position')->nullable(true);
            $table->string('site')->nullable(true);
            $table->string('manager')->nullable(true);
            $table->dateTime('execution_date')->nullable(true);
            $table->tinyInteger('execution_type')->nullable(true);
            $table->string('remarks', 1000)->nullable(true);
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
        Schema::dropIfExists('potential_triggers');
    }
}
