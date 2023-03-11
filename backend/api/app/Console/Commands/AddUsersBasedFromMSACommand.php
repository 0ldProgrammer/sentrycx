<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\Maintenance\Services\UserService;
use Illuminate\Container\Container;

class AddUsersBasedFromMSACommand extends Command
{
    /** @var UserService $service  */
    protected $service;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Insert new users to the database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Container $container){
        $this -> service = $container -> get('UserService');
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(){
        // insert new users
        $stats = $this -> service -> insertUsersBasedOnMsa();

        echo "Done inserting data into users table" . PHP_EOL;
    }
}
