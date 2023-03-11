<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventApprovalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('event_approvals', function (Blueprint $table) {
            $table->id();
            $table->string('requested_by');
            $table->string('event');
            $table->string('agent_name');
            $table->boolean('status');
            $table->string('approved_by');
            $table->string('params','1000');
            $table->integer('worker_id')->nullable();
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
        //
        Schema::dropIfExists('event_approvals');
    }

}
