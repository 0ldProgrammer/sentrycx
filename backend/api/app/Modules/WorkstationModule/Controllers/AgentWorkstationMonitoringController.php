<?php

namespace App\Modules\WorkstationModule\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Container\Container;
use App\Http\Controllers\Controller as BaseController;

class AgentWorkstationMonitoringController extends BaseController {

    /** @var \App\Modules\WorkstationModule\Services\MonitoringService $service */
    protected $service;


    /**
     *
     * Constructor dependencies
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct(Container $container){
        $this -> service = $container -> get('MonitoringService');

        $this -> applicationService = $container -> get('ApplicationService');
    }

    /**
     *
     * Undocumented function long description
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __invoke(Request $request, $worker_id){
        $monitoring_id = $request -> input('monitoringID');

        return [
            'status' => 'OK',
            'data' => $this -> service -> getMonitoringData($monitoring_id)
        ];

    //     $account = $request -> query('account');
    //     $data = $this -> service -> getAll($worker_id);

    //     // $app_status = collect($data) -> where;

    //     // echo '<pre>'; print_r($app_status); die();
    //     return [
    //         'status' => 'OK',
    //         'data'   => $data,
    //         'applications' => $this -> applicationService -> getApplications($account)
    //    ];
    }
}
