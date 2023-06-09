<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFlagCodes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flag_codes', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('category_id');
            $table->string('name');
            $table->string('description') -> nullable();
            $table->boolean('active') -> default(TRUE);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('flag_codes');
    }
}
