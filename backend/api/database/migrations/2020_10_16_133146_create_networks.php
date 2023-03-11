<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNetworks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('networks'))
            return;

        Schema::create('networks', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('description')->nullable();
            $table->string('url')->nullable();
            $table->string('ip')->nullable();
            $table->integer('account_id');
            $table->string('account')->nullable();
            $table->boolean('is_active')->nullable();
            $table->integer('category_id')->nullable();;
            $table->integer('code_id')->nullable();;
            $table->timestamp('date_created')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('networks');
    }
}
