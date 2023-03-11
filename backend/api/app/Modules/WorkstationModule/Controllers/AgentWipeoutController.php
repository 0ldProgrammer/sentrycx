<?php

namespace App\Modules\WorkstationModule\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\WorkstationModule\Events\AgentWipeoutBroadcast;
use Illuminate\Container\Container;
use App\Modules\WorkstationModule\Services\AgentConnectionService;
use App\Modules\WorkstationModule\Services\EventApprovalService;
use \Firebase\JWT\JWT;

class AgentWipeoutController extends Controller {
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
        $password = $request -> only('password', 'execution_date', 'event');

        // TODO : Include offline broadcast
        $agentDetails = $this -> service -> getConnectionByEmployee($worker_id);
        event( new AgentWipeoutBroadcast( $agentDetails->session_id, $password ) );


        return [
            'status' => 'OK', 
            'msg'    => 'Wiping out of the station is now on progress.', 
            'debug'  => $request -> all()
        ];
    }
}
