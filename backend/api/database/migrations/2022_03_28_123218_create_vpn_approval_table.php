<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVpnApprovalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vpn_approval', function (Blueprint $table) {
            $table->id();
            $table->integer('worker_id');
            $table->string('name')->nullable();
            $table->string('email', 150)->nullable();
            $table->string('status', 50)->nullable();
            $table->string('action_taken_by')->nullable();
            $table->string('remarks')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('vpn_approval');
    }
}
