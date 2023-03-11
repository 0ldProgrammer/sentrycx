<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOnlineStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('online_stats', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('connected');
            $table->integer('wireless');
            $table->integer('wired');
            $table->integer('wah');
            $table->integer('bm');
            $table->string('type')-> default('HOURLY');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('online_stats');
    }
}
