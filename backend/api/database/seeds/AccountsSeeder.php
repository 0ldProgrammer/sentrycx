<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * TODO : Create a base InverseSeeder Class which you can
     *      just inject a property for the json file and table names
     *      Inversed Seeder Classes will just then extends to the said base class
     *
     * @return void
     */
    public function run()
    {
        $json_data   = file_get_contents(resource_path() . '/seeds/accounts.json');
        $insert_data = json_decode( $json_data );

        foreach( $insert_data as $row ){
            DB::table('accounts')->insert( (array) $row );
        }
    }
}
