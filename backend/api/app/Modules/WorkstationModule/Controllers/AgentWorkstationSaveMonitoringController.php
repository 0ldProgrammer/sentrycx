<?php

namespace App\Modules\WorkstationModule\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller as BaseController;
use Illuminate\Container\Container;
use App\Modules\WorkstationModule\Events\AgentDashboardBroadcast;

class AgentWorkstationSaveMonitoringController extends BaseController {

    /** @var \App\Modules\WorkstationModule\Services\MonitoringService $var description */
    protected $service;


    /** @var \App\Modules\WorkstationModule\Services\WorkstationService $var description */
    protected $workstationService;
    /**
     *
     * Constructor Method
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct(Container $container){
        $this -> service = $container -> get('MonitoringService');

        $this -> workstationService = $container -> get('WorkstationService');
    }

    /**
     *
     * Handles the saving of workstation monitoring
     *
     * @param Request $request
     * @param Integer $worker_id
     * @return Response
     * @throws conditon
     **/
    public function __invoke(Request $request, $worker_id = 0){
        $data = $request -> only('application','ping_ref', 'ping','traceroute_ref', 'traceroute','mtr_ref', 'mtr', 'type', 'ram', 'memory', 'disk_drive');

        if( $data['type'] == 'AUTO'){
            $this -> workstationService -> updateThresholdStatus( $worker_id );
            event(new AgentDashboardBroadcast($data));
        }


        $this -> service -> save( $worker_id, $data );

        return ['status' => 'OK', 'msg' => 'Monitoring details has been saved.', 'data' => $data ];
    }

}
