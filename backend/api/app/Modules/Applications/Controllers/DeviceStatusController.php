<?php

namespace App\Modules\Applications\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Container\Container;
use Laravel\Lumen\Routing\Controller as BaseController;

class DeviceStatusController extends BaseController {

    /** @var App\Modules\Applications\Services\ApplicationService  $applicationService description */
    protected $deviceStatusService;
    /**
     * Constructor Method
     * Define constructor dependencies here
     *
     * @return void
     **/
    public function __construct(Container $container ){
        $this -> deviceStatusService = $container -> get ('DeviceStatusService');
    }


    /**
     * Handles the Code list
     *
     * @param Request $request
     * @return Response
     * @throws conditon
     **/
    public function __invoke( Request $request)
    {

        $socket_id = $request -> input('socket_id');

        if($socket_id!="")
        {
            $employee_id = $this -> deviceStatusService -> getRedisData($socket_id, 'worker_id');
            $dateTime = $this -> deviceStatusService -> getRedisData($socket_id, 'dateTime');

            $redisDetails = [
                'id'        => $socket_id,
                'worker_id' => $employee_id,
                'dateTime'  => $dateTime,
                'count'     => 1
            ];

            $this -> deviceStatusService -> removeRedisData('DeviceStatus.'.$socket_id);
            $this -> deviceStatusService -> storeRedis($socket_id, $redisDetails);
        }else{

            $devices = $this -> deviceStatusService -> getOnlineAgents();
            foreach($devices as $device)
            {
                $count = $this -> deviceStatusService -> getRedisData($device->session_id, 'count');
                if($count=="")
                {
                    $redisDetails = [
                        'id'        => $device->session_id,
                        'worker_id' => $device->worker_id,
                        'dateTime'  => strtotime($device->updated_at),
                        'count'     => 1
                    ];
                    $this -> deviceStatusService -> storeRedis($device->session_id, $redisDetails);
                }

            }

            $redis = $this -> deviceStatusService -> retrieveRedisData();
            if(!empty($redis))
            {
                foreach($redis as $device)
                {

                    $count = $this -> deviceStatusService -> getRedisData($device['id'], 'count');
                    print_r($device);
                    
                }
            }else{
                echo 'Redis is empty';
            }

        }
        
        
    }
}
