<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LogsWpAgentPublic extends Migration
{
	const DB_TABLE = 'logs_workstation_profile';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		// to fix table issue - drop existing column
		if (Schema::hasColumn(self::DB_TABLE, 'created_at')) {
			Schema::table(self::DB_TABLE, function (Blueprint $table)
            {
				$table->dropColumn(['created_at']);
            });
        }

		if (Schema::hasColumn(self::DB_TABLE, 'updated_at')) {
			Schema::table(self::DB_TABLE, function (Blueprint $table)
            {
				$table->dropColumn(['updated_at']);
            });
        }

		if (Schema::hasColumn(self::DB_TABLE, 'deleted_at')) {
			Schema::table(self::DB_TABLE, function (Blueprint $table)
            {
				$table->dropColumn(['deleted_at']);
            });
        }

		// add softDeletes()
		// add timestamps()
		Schema::table(self::DB_TABLE, function (Blueprint $table) {
			$table->string('public_ip', 20)->nullable();
			$table->string('country', 50)->nullable();
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
        Schema::table(self::DB_TABLE, function (Blueprint $table) {
			$table->dropColumn([
				'public_ip',
				'country',
				'region',
				'city',
				'zip_code',
				'latitude',
				'longitude',
			]);
        });

		// retain fields created by softDeletes()
		// retain fields created by timestamps()
    }
}
