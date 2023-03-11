<?php

namespace App\Modules\WorkstationModule\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Modules\WorkstationModule\Services\WorkstationService;
use App\Http\Controllers\Controller as BaseController;
use Illuminate\Container\Container;

class AgentSaveMediaStatsController extends BaseController{

    /** @var WorkstationService $service */
    protected $service;

    /**
     *
     * Dependency constructor method
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct(Container $container){
        $this -> service = $container -> get('MediaDeviceService');
    }

    /**
     *
     * Handles the updating of agent media devices stats
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __invoke(Request $request, $worker_id = 0){
        $args = $request -> only('audio', 'video', 'mic', 'remarks');

        $this -> service -> updateMediaDevice( $worker_id, $args );

        return [ 'status' => 'OK', 'msg' => 'Successfully updated.' ];
    }

}
