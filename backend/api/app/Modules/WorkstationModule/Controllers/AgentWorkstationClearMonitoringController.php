<?php

namespace App\Modules\WorkstationModule\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Container\Container;
use App\Http\Controllers\Controller as BaseController;
use App\Modules\WorkstationModule\Services\MonitoringService;

class AgentWorkstationClearMonitoringController extends BaseController {
    /** @var MonitoringService $service */
    protected $service;

    /**
     *
     * Constructor Dependencies
     *
     * @param Container $container
     * @return type
     * @throws conditon
     **/
    public function __construct(Container $container){
        $this -> service = $container -> get('MonitoringService');
    }

    /**
     *
     * Handles the clearing of the Monitoring data by workerID
     *
     * @param Request $request
     * @param Integer $worker_id
     * @return Response
     * @throws conditon
     **/
    public function __invoke(Request $request, $worker_id){
        $type = $request -> input('type', 'AUTO');

        $app  = $request -> input('app', false);

        $this -> service -> clear( $worker_id, $type, $app );

        return ['status' => 'OK', 'msg' => 'Application stats has been cleared'];
    }
}
