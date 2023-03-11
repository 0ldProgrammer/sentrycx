<?php

namespace App\Modules\WorkstationModule\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Container\Container;
use App\Modules\WorkstationModule\Events\AgentMTRUpdatedBroadcast;

class AgentProgressController extends Controller {
    /** @var \App\Modules\WorkstationModule\Services\WorkstationService $workstationService */
    protected $workstationService = null;
    /**
     *
     * Setup Dependencies
     *
     * @param Type $var Description
     * @return Response
     * @throws conditon
     **/
    public function __construct( Container $container  ){
        $this -> workstationService = $container -> get('WorkstationService');
    }


    /**
     *
     * Handles the route controller
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __invoke(Request $request, $worker_id = null ) {
        $percentage = $request -> input('percentage');

        $this -> workstationService -> progress( $worker_id, $percentage );

        $this -> workstationService -> checkPercentage($percentage, $worker_id);

        $this -> _dispatch( $worker_id );

        return ['status' => 'OK'];
    }


    /**
     *
     * Dispatch the MTR Updated
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    private function _dispatch( $worker_id = 0 ){
        $data = [ 'worker_id' => $worker_id ];

        event( new AgentMTRUpdatedBroadcast($data) );
    }

}
