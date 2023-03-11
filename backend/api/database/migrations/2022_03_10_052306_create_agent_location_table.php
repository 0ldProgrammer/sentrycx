<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgentLocationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agent_location', function (Blueprint $table) {
            $table->id();
            $table->integer('worker_id');
            $table->string('country', 50)->nullable();
            $table->string('country_code', 50)->nullable();
            $table->string('neighbourhood', 100)->nullable();
			$table->string('region', 50)->nullable();
			$table->string('city', 50)->nullable();
			$table->string('zip_code', 15)->nullable();
			$table->decimal('latitude', 8, 6)->nullable();
			$table->decimal('longitude', 9, 6)->nullable();
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
        Schema::dropIfExists('agent_location');
    }
}
