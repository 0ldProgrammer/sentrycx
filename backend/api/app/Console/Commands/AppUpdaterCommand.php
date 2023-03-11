<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\WorkstationModule\Events\AgentAppUpdateBroadcast;
use Illuminate\Container\Container;
use App\Modules\WorkstationModule\Services\WorkstationService;
use App\Modules\Flags\Events\DesktopNotificationBroadcast;

class AppUpdaterCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'desktop:update {version} { session_id? : Socket ID of a specific user to simulate update }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Broadcast a restart event to online agents forcing them to update their app';

    /**
     * Instance of WorkstationService
     *
     * @var WorkstationService
     */
    protected $service;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Container $container)
    {
        $this -> service = $container -> get('WorkstationService');
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $version = $this -> argument('version');
        $session_id = $this -> argument('session_id');

        if( $session_id ){
            echo "Restarting session ({$session_id})". PHP_EOL;
            $this -> _dispatch( $session_id );
            return;
        }

        echo "Checking outdated desktop versions". PHP_EOL;

        $workstations = $this -> service -> getOnlineWorkstation($version);

        echo "Outdated Workstation : {$workstations->count()}". PHP_EOL;
        foreach( $workstations as $workstation ){
            echo "Restarting {$workstation->agent_name} ({$workstation->session_id}) ". PHP_EOL;
            $this -> _dispatch( $workstation -> session_id );
        }
    }

    /** 
     *
     * Dispatch a reset update 
     *
     * @param String $session_id
     * @return type
     * @throws conditon
     **/
    private function _dispatch($session_id){
        event( new DesktopNotificationBroadcast([
            'title'   => 'APP UPDATE',
            'message' => 'An updated version of SentryCX is now available. Click here to update.',
            'session_id' => $session_id,
            'url'     => null
        ]));
    }
}
