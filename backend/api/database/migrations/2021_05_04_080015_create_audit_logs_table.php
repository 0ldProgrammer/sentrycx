<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('user')->nullable();
            $table->string('event')->nullable();
            $table->timestamp('date_triggered')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('affected_agent')->nullable();
            $table->string('workstation_number')->nullable();
            $table->integer('worker_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('audit_logs');
    }
}
