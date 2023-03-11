<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlaybookTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('playbook', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('client');
            $table->string('url');
        });
    }

    /**
     * Reverse the migrations.z
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('playbook');
    }
}
