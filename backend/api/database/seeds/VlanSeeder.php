<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $json_data   = file_get_contents(resource_path() . '/seeds/vlan.json');
        $insert_data = json_decode( $json_data );

        foreach( $insert_data as $row ){
            DB::table('vlan')->insert($row );
        }
    }
}
