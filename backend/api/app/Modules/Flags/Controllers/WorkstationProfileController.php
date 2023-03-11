<?php

namespace App\Modules\Flags\Controllers;

use Illuminate\Http\Request;
use Illuminate\Container\Container;
use App\Http\Controllers\Controller;

class WorkstationProfileController {

    /** @var \App\Modules\WorkstationModule\Services\WorkstationService $service  */
    protected $service = null;

    /** @var \App\Modules\WorkstationModule\Services\EventLogService $service  */
    protected $eventService = null;

    /**
     * Constructor Dependencies
     *
     * @param Container $container
     * @return type
     * @throws conditon
     **/
    public function __construct( Container $container ){
        $this -> service = $container -> get('WorkstationService');
        $this -> eventService = $container -> get ('EventLogService');
        $this -> logService         = $container -> get ('ZohoLogService');
        $this -> speedtestService   = $container -> get('SpeedtestRecordsService');
    }


    /**
     * Handles the route function
     *
     * @param Request $request
     * @param int $id , Flag ID
     * @return \Illuminate\Http\Response
     * @throws conditon
     **/
    public function __invoke( Request $request, $id = 0 ){
    $page = $request -> query('page', 1);

    $page = ( is_numeric( $page ) ) ? $page : 1;

    $profile = $this -> service -> getProfile( $id, $page );

    $worker_id = json_decode( $profile -> toJson() )->data[0] -> worker_id;

    $conditions = ['worker_id' =>  $worker_id  ];

        return [
            'profile'    => $profile,
            'lezap'      => $this -> service -> getLezap( $conditions ),
            'progress'   => $this -> service -> getProgress( $worker_id, 2 ),
            'event-logs' => $this -> eventService -> getLogs( $worker_id ),
            'zoho-logs'  => $this -> logService -> getByWorkerID( $worker_id ),
            'media-devices' => $this -> service -> getMediaDevice( $worker_id ),
            'speedtest' => $this -> speedtestService -> setWorkerID($worker_id) -> recent()
        ];
    }
}
