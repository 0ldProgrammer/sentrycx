<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FlagCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('flag_categories') -> insert([
            ['id' => 1, 'name' => 'Voice', 'description' => 'Voice', 'active' => true ],
            ['id' => 2, 'name' => 'Network', 'description' => 'Network', 'active' => true ],
            ['id' => 3, 'name' => 'Application', 'description' => 'Application', 'active' => true ]]);
    }
}
