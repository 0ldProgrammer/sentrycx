<?php
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class PlaybookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('playbook') -> insert([
            [ 'client' => 'Belkin', 'url' => 'https://cnxmail.sharepoint.com/:o:/s/AAA_ITSDM/Erc7YoUTHDVOjgxQ37qYH-EBF0kPVJ3sZugo3HJaf0Q6lw'],
            [ 'client' => 'Grabtaxi', 'url' => 'https://cnxmail.sharepoint.com/:o:/s/aarp_itsdm/EtNuLMv51NdIpp5VdQev6S8BRs8MtCqyCCeo1m1elkGrWA'],
            [ 'client' => 'Razer', 'url' => 'https://bit.ly/3cjDawI'],
            [ 'client' => 'Anthem', 'url' => 'https://cnxmail.sharepoint.com/:o:/s/AboutYou/ElOh-jELL-lLux1hYPEJj4YBi4iZHvKKt7CtjS-Nl0Oslg?e=LKHybo'],
            [ 'client' => 'Apple', 'https://cnxmail.sharepoint.com/:o:/s/ACN_ITSDM2/EuWJ7e8rw25NmFhzV9lhN6YBtE0e64j8vwh0UIs-oeR0sQ']
        ]);
    }
}
