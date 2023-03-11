<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFlags extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flags', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('category_id');
            $table->integer('code_id');
            $table->integer('employee_id');
            $table->string('email')->nullable();
            $table->string('name')->nullable();
            $table->string('country')->nullable();
            $table->string('account')->nullable();
            $table->string('isp')->nullable();
            $table->string('location')->nullable();
            $table->string('gateway')->nullable();
            $table->string('vlan')->nullable();
            $table->string('dns_1')->nullable();
            $table->string('dns_2')->nullable();
            $table->string('station_id')->nullable();
            $table->string('status')->nullable();
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('flags');
    }
}
