<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateZohoReports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zoho_reports', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('worker_id')->nullable();
            $table->string('viewer_type')->nullable();
            $table->string('agent_email')->nullable();
            $table->string('agent_ipaddress')->nullable();
            $table->string('video_available')->nullable();
            $table->string('agent_os')->nullable();
            $table->integer('duration')->nullable();
            $table->string('session_title')->nullable();
            $table->string('video_size')->nullable();
            $table->string('session_type')->nullable();
            $table->string('viewer_email')->nullable();
            $table->string('viewer_os')->nullable();
            $table->string('video_deleted')->nullable();
            $table->string('zb_transferred')->nullable();
            $table->string('session_owner_email')->nullable();
            $table->string('video_complete')->nullable();
            $table->string('end_time')->nullable();
            $table->string('session_id')->nullable();
            $table->string('display_name')->nullable();
            $table->string('tsid')->nullable();
            $table->string('video_render_problem')->nullable();
            $table->string('agent_type')->nullable();
            $table->string('start_time')->nullable();
            $table->string('video_requested_time')->nullable();
            $table->string('viewer_ipaddress')->nullable();
            $table->string('session_notes')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('zoho_reports');
    }
}
