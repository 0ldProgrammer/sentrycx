<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OptionsListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $json_data   = file_get_contents(resource_path() . '/seeds/options_list.json');
        $insert_data = json_decode( $json_data );

        foreach( $insert_data as $row ){
            DB::table('options_list')->insert( (array) $row );
        }



    }
}
