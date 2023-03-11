<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AgentApplicationsMailCommand extends Command
{
    /** @var DeviceStatusService $service  */
    protected $service;
    /**
     * The name and signature of the console command.
    *
    * @var string
    */
    protected $signature = 'mail:send-applications';

    /**
     * The console command description.
    *
    * @var string
    */
    protected $description = 'daily email for agents required and restricted application';

    /**
     * Create a new command instance.
    *
    * @return void
    */
    public function __construct(Container $container){
        $this -> service = $container  -> get('AgentConnectionService');
        parent::__construct();
    }

    /**
     * Execute the console command.
    *
    * @return mixed
    */
    public function handle() {
       
        $this -> service -> sendEmailForApplications();

    }

}