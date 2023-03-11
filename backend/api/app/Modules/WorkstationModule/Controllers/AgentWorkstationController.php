<?php

namespace App\Modules\WorkstationModule\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Container\Container;
use Laravel\Lumen\Routing\Controller as BaseController ;
use App\Modules\WorkstationModule\Services\WorkstationService;
use App\Modules\HistoricalRecords\Services\SpeedtestRecordsService;

class AgentWorkstationController extends BaseController {
    /** @var  WorkstationService  $workstationService */
    protected $workstationService;
    /**
     * Constructor Method
     * Define constructor dependencies here
     *
     * @return void
     **/
    public function __construct(Container $container ){
        $this -> workstationService = $container -> get ('WorkstationService');
        $this -> eventLogService    = $container -> get ('EventLogService');
        $this -> logService         = $container -> get ('ZohoLogService');
        $this -> speedtestService   = $container -> get('SpeedtestRecordsService');
    }


    /**
     * Handles the Application list
     *
     * @param Request $request
     * @return Response
     * @throws conditon
     **/
    public function __invoke( Request $request)
    {
        $worker_id = $request -> query('worker_id');

        $condition = ['worker_id' => $worker_id ];

        return [
            'status' => 'OK',
            'data'   => $this -> workstationService -> getWorkstation( ['wp.worker_id' => $worker_id ]),
            'lezap'  => $this -> workstationService -> getLezap( $condition ),
            'event-logs' => $this -> eventLogService -> getLogs( $worker_id ),
            'zoho-logs' => $this -> logService -> getByWorkerID( $worker_id ),
            'media-devices' => $this -> workstationService -> getMediaDevice( $worker_id ),
            'speedtest' => $this -> speedtestService -> setWorkerID($worker_id) -> recent()
        ];
    }

}
