<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\Maintenance\Services\UserService;
use Illuminate\Container\Container;

class RemoveUsersBasedFromMSACommand extends Command
{
    /** @var UserService $service  */
    protected $service;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remove:users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove users and msa to the database';

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
        // remove users
        $stats = $this -> service -> removeUsersBasedOnMsa();

        echo "Done removing data into users table" . PHP_EOL;
    }
}
