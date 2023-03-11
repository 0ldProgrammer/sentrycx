<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCodesPerAccount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('codes_per_account', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('options_list_id')->default(NULL);
            $table->string('account')->default(NULL);
        });

        // CREATE TABLE `codes_per_account` (
        //     `id` int(11) NOT NULL AUTO_INCREMENT,
        //     `options_list_id` int(11) DEFAULT NULL,
        //     `account` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
        //     PRIMARY KEY (`id`)
        //   ) ENGINE=InnoDB AUTO_INCREMENT=82 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('codes_per_account');
    }
}
