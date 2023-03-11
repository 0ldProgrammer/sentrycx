<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FlagCodesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /*
         * Category ID referrence
         *  1 - Voice
         *  2 - Network
         *  3 - Application
         */
        DB::table('flag_codes')->insert([
            ['id' => 1, 'category_id' => 1, 'name' => ' Call Disconnections' ],
            ['id' => 2, 'category_id' => 1, 'name' => 'One Way Audio'],
            ['id' => 3, 'category_id' => 1, 'name' => 'Ghost Calls' ],
            ['id' => 4, 'category_id' => 1, 'name' => 'Unable to Login'],
            ['id' => 5, 'category_id' => 1, 'name' => 'Discover mode'],
            ['id' => 6, 'category_id' => 2, 'name' => 'Slow Internet'],
            ['id' => 7,'category_id' => 2, 'name' => 'Intermittent Connection'],
            ['id' => 8,'category_id' => 2, 'name' => 'Site Unreachable'],
            ['id' => 9, 'category_id' => 3, 'name' => 'Unreachable'],
            ['id' => 10, 'category_id' => 3, 'name' => 'Not Loading'],
            ['id' => 11,'category_id' => 3, 'name' => 'Slow Access'],
            ['id' => 12,'category_id' => 3, 'name' => 'Unable to Login']
        ]);
    }
}
