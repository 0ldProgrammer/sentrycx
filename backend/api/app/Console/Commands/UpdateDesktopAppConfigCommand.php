<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Container\Container;
use App\Modules\WorkstationModule\Events\AgentCommandLineBroadcast;
use App\Modules\WorkstationModule\Services\AgentConnectionService;
use Illuminate\Support\Facades\Log;

class UpdateDesktopAppConfigCommand extends Command
{
    protected $service;
    /**
     * The name and signature of the console command.
    *
    * @var string
    */
    protected $signature = 'update:app-config';

    /**
     * The console command description.
    *
    * @var string
    */
    protected $description = 'A Cron that updates Desktop App Config File';

    /**
     * Create a new command instance.
    *
    * @return void
    */
    public function __construct(Container $container){
        $this -> agentService = $container  -> get('AgentConnectionService');
        $this -> commandService = $container -> get('CommandLineService');
        
        parent::__construct();
    }

    /**
     * Execute the console command.
    *
    * @return mixed
    */
    public function handle()
    {
        $workstation_profile = $this -> commandService -> getDesktopVersion('2.1.5');
        
        foreach($workstation_profile as $wp)
        {
            $worker_id = $wp -> wid;
            $account = $wp -> account;
            $session_id = $wp -> session_id;
            
            $this -> commandService -> setWorkerID( $worker_id );

            $command = 'Invoke-WebRequest -Uri "https://sentrycx.s3.ap-southeast-1.amazonaws.com/SentryCX.exe.config" -OutFile "$env:TEMP\SentryCX.exe.config"; Remove-Item "$env:USERPROFILE\AppData\Local\SentryCX\app-2.1.5\SentryCX.exe.config"; Move-Item -Path "$env:TEMP\SentryCX.exe.config" -Destination "$env:USERPROFILE\AppData\Local\SentryCX\app-2.1.5\SentryCX.exe.config";';
           
    
            $type = 'Powershell';
    
            $data = $this -> commandService -> logCommand( $command, $type );

            $this -> commandService -> logCronUpdate($worker_id, $account , $session_id);
    
            $this -> _dispatch( $worker_id, $data);
            
            // dd($worker_id);

        }
        

        
    }


    public function _dispatch($worker_id, $data ){
        $session_id = $this -> agentService -> getAgentSession( $worker_id );
        // echo $session_id . '<br />';
        event( new AgentCommandLineBroadcast($session_id, $data ));
        // event( new AgentCommandLineBroadcast('aRbpBBSSdZKPST2TAAAH', $data ));

    }
}