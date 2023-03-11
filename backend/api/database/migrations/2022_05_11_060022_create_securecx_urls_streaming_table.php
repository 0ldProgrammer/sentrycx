<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSecurecxUrlsStreamingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('securecx_urls_streaming', function (Blueprint $table) {
            $table->id();
            $table->string('worker_id', 100);
            $table->string('streaming_primary_url');
            $table->string('streaming_primary_host_name', 150);
            $table->smallInteger('streaming_primary_port');
            $table->string('streaming_primary_telnet_result', 10);
            $table->string('streaming_secondary_url');
            $table->string('streaming_secondary_host_name', 150);
            $table->smallInteger('streaming_secondary_port');
            $table->string('streaming_secondary_telnet_result', 10);
            $table->string('status_primary_url');
            $table->string('status_primary_host_name', 150);
            $table->smallInteger('status_primary_port');
            $table->string('status_primary_telnet_result', 10);
            $table->string('status_secondary_url');
            $table->string('status_secondary_host_name', 150);
            $table->smallInteger('status_secondary_port');
            $table->string('status_secondary_telnet_result', 10);
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
        Schema::dropIfExists('securecx_urls_streaming');
    }
}
