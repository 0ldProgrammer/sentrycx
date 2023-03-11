<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Container\Container;
use App\Modules\WorkstationModule\Services\AgentConnectionService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\MailOnlineAgents;

class HourlyEmailSend extends Command
{
    /** @var DeviceStatusService $service  */
    protected $service;
    /**
     * The name and signature of the console command.
    *
    * @var string
    */
    protected $signature = 'email:hourlySend';

    /**
     * The console command description.
    *
    * @var string
    */
    protected $description = 'CRON hourly email to extract Agent details that is online';

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
    public function handle(){

    	
    	$myEmail = getenv('MAIL_TO_HELPDESK');
    	Mail::to($myEmail)
            ->cc([getenv('MAIL_CC_HELPDESK')])
            ->send(new MailOnlineAgents($this->service));

    	dd("Mail Send Successfully");
    }
}