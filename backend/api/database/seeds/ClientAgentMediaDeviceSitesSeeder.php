<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClientAgentMediaDeviceSitesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('agent_media_device_sites') -> insert([
            [ "url" => 'https://stg.emed.com/' , "account" => "OPEXOTHER" ],
            [ "url" => 'https://proctor-api.stg.emed.com/users/sign-in.' , "account" => "OPEXOTHER" ],
            [ "url" => 'https://emedsupport.zendesk.com' , "account" => "OPEXOTHER" ],
            [ "url" => 'https://proctor-api.emed.com/users/sign-in' , "account" => "OPEXOTHER" ],
            [ "url" => 'https://app.emed.com/app/login?redirect_to=%2Fapp%2Fstart-testing' , "account" => "OPEXOTHER" ],
            [ "url" => 'https://app.chime.aws/check' , "account" => "OPEXOTHER" ]
        ]);
    }
}
