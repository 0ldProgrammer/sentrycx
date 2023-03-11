<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWorkstationActionTakenAtToVpnApprovalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vpn_approval', function (Blueprint $table) {
            $table->string('workstation')->after('email')->nullable();
            $table->dateTime('action_taken_at')->after('action_taken_by')->nullable();
            $table->string('action_taken_remarks')->after('remarks')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vpn_approval', function (Blueprint $table) {
            //
        });
    }
}
