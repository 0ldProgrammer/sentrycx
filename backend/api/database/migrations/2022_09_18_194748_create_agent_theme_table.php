<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgentThemeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agent_theme', function (Blueprint $table) {
            $table->id();
            $table->string('worker_id', 15);
            $table->string('filterThemeName');
            $table->string('themeName');
            $table->boolean('miniSidebar');
            $table->boolean('sidebarImage');
            $table->string('leftSidebarImg');
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
        Schema::dropIfExists('agent_theme');
    }
}
