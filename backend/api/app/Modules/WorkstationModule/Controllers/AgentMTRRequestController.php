<?php

namespace App\Modules\WorkstationModule\Controllers;

use Illuminate\Http\Request;
use Illuminate\Container\Container;
use App\Http\Controllers\Controller;
use App\Modules\WorkstationModule\Events\AgentMTRRequestBroadcast;

class AgentMTRRequestController extends Controller {

    /** @var \App\Modules\WorkstationModule\Services\WorkstationService $workstationService description */
    protected $workstationService = null;

    /**
     *
     * Constructor Dependencies
     *
     * @param Container $container
     * @return type
     * @throws conditon
     **/
    public function __construct(Container $container){
        $this -> workstationService = $container -> get('WorkstationService');
    }

    /**
     *
     * Handles the trigger for sending MTR Request to agent desktop
     *
     * @param Request $request Description
     * @return type
     * @throws conditon
     **/
    public function __invoke(Request $request ){
        $form_data  = $request -> only('sessionID', 'workerID', 'host', 'hops', 'worker_id');

        $ip_address = gethostbyname( $form_data['host']);

        if( !filter_var($ip_address, FILTER_VALIDATE_IP ))
            return response()->json(['status' => 'ERROR', 'msg' => 'Not a valid host'], 422);

        $this -> workstationService -> process( $form_data['sessionID'], TRUE, $form_data['host'] );

        $this -> workstationService -> progress( (int)$form_data['worker_id'], 1 );

        $this -> _dispatchRequest([
            'mtr_host'   => $form_data['host'],
            'mtr_hops'       => $form_data['hops'],
            'session_id' => $form_data['sessionID'],
            'ip'         => $ip_address,
        ]);

        return ['data' => [], 'status' => 'OK' , 'debug' => gethostbyname($form_data['host'] )];
    }

    private function _dispatchRequest( $data ){
        event(new AgentMTRRequestBroadcast($data));


    }


}
