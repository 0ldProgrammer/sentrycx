<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call('FlagCodesSeeder');
        // $this->call('FlagCategoriesSeeder');
        $this->call('AgentNetworkTableSeeder');
    }
}
