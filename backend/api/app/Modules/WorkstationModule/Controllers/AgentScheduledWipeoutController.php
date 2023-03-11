<?php

namespace App\Modules\WorkstationModule\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\WorkstationModule\Events\AgentScheduledWipeoutBroadcast;
use Illuminate\Container\Container;
use App\Modules\WorkstationModule\Services\AgentConnectionService;



class AgentScheduledWipeoutController extends Controller {
    /** @var AgentConnectionService $var description */
    protected $service;

    /**
     *
     * Constructor Dependencies
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct( Container $container ){
        $this -> service = $container -> get('AgentConnectionService');
    }

    /**
     *
     * Handles the Agent Wipeout Endpoint
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __invoke($worker_id=0, Request $request){
        $params = $request -> only('password', 'execution_date');
        

        $this -> _dispatch( $worker_id, $params );
        // TODO : Include offline broadcast
        // $agentDetails = $this -> agentConnection -> getConnectionByEmployee($worker_id);
        // event( new AgentWipeoutBroadcast( $agentDetails->session_id, $password ) );

        return [
            'status' => 'OK', 
            'msg'    => 'Scheduled wipeout request has been sent to agent.', 
            'debug'  => $request -> all()
        ];
    }

    /**
     *
     * Dispatch the ScheduledWipeoutBroadcast
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    private function _dispatch( $worker_id , $params ){
        $connection = $this -> service ->  getConnectionByEmployee( $worker_id );

        if( $connection -> is_active ){
            // echo "DISPATCHING $worker_id : ONLINE ";
            event( new AgentScheduledWipeoutBroadcast($connection -> session_id, $params) );
            return;
        }

        $events = [];

        $events[] = [
            'worker_id' => $connection -> worker_id,
            'event_name' => "App\Modules\WorkstationModule\Events\AgentScheduledWipeoutBroadcast",
            'parameters' => json_encode( $params )
        ];
        // echo "DISPATCHING $worker_id : OFFLINE ";

        $this -> offlineQueueService -> queueEvents($events);
    }
}
