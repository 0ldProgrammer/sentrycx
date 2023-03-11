<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAutomatedUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('automated_users', function (Blueprint $table) {
            $table -> id();
            $table -> timestamps();
            $table -> string('programme_msa')->nullable();
            $table -> string('tagging')->nullable();
            $table -> string('count_users')->nullable();
            $table -> string('type')->default('programme_msa');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('automated_users');
    }
}
