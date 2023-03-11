<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSecurecxUrl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('securecx_url', function (Blueprint $table) {
            $table ->id();
            $table ->timestamps();
            $table -> string('name')->nullable(); 
            $table -> string('url')->nullable();
            $table -> string('account')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('securecx_url');
    }
}
