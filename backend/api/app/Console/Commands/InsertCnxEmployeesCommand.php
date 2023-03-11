<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Container\Container;
use App\Modules\Workday\Services\EmployeeService;

class InsertCnxEmployeesCommand extends Command
{
    /** @var EmployeeService $service  */
    protected $service;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'insert:cnx-employees';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Insert to cnx_employees table';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Container $container){
        $this -> service = $container -> get('EmployeeService');
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(){
        // insert cnx employees
        $this -> service -> insertCnxEmployees();
        echo "Done inserting data into cnx_employees table" . PHP_EOL;
    }
}
