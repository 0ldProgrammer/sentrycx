<?php

namespace App\Modules\WorkstationModule\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller as BaseController;
use Illuminate\Container\Container;
use App\Modules\WorkstationModule\Events\AgentWorkstationMonitoringRequestBroadcast;

class AgentWorkstationRequestMonitoringController extends BaseController {

    /** @var \App\Modules\WorkstationModule\Services\WorkstationService $service **/
    protected $service;

    /**
     *
     * Constructor Dependencies
     *
     * @param Container $container
     * @return void
     * @throws conditon
     **/
    public function __construct( Container $container){
        $this -> service = $container -> get('WorkstationService');
    }

    /**
     *
     * Handles the monitoring request triggered by TOC
     *
     * @param Request $request
     * @param Integer $worker_id
     * @return Response
     * @throws conditon
     **/
    public function __invoke(Request $request, $worker_id = 0 ){
        $session_id = $this -> service -> getAgentSession( $worker_id );

        $application = $request -> input('application', false);

        event( new AgentWorkstationMonitoringRequestBroadcast($session_id, $application) );

        return ['status' => 'OK', 'msg' => "Monitoring request has been sent to $worker_id"];
    }
}
