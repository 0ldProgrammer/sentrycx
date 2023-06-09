<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsActiveInAuxListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('aux_list', function (Blueprint $table) {
            $table -> boolean('is_active')->nullable();
            $table -> boolean('is_default')->nullable();
            $table -> integer('aux_order')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('aux_list', function (Blueprint $table) {
            $table -> removeColumn('is_active');
            $table -> removeColumn('is_default');
            $table -> removeColumn('aux_order');
        });
    }
}
