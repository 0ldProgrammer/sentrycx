<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRedflagDashboard extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('redflag_dashboard') )
            return;

        Schema::create('redflag_dashboard', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->text('agent_username')->nullable();
            $table->text('agent_name')->nullable();
            $table->text('lob')->nullable();
            $table->text('option_inquiry')->nullable();
            $table->text('timestamp_submitted')->nullable();
            $table->text('timestamp_acknowledged')->nullable();
            $table->text('timestamp_closed')->nullable();
            $table->text('status_info')->nullable();
            $table->boolean('viewed')->nullable();
            $table->integer('code_id');
            $table->integer('agent_id')->nullable()->default(0);
            $table->boolean('confirmed')->nullable();
            $table->string('account')->nullable();
            $table->integer('worker_id')->nullable();
            $table->string('location')->nullable();
            $table->string('country')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('redflag_dashboard');
    }
}
