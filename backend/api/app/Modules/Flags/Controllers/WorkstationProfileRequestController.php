<?php

namespace App\Modules\Flags\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Container\Container;
use App\Modules\Flags\Events\AgentWorkstationRequestBroadcast;

class WorkstationProfileRequestController extends Controller {
    /** @var \App\Modules\WorkstationModule\Services\WorkstationService $workstationService  */
    protected $workstationService = null;

    /**
     * Constructor dependencies
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct( Container $container )
    {
        $this -> workstationService = $container -> get('WorkstationService');
    }

    /**
     * Handles the route function
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __invoke(Request $request, $id = 0 ) {
        $params = $request -> only('selected_host', 'selected_ip' );

        $worker_id = $request -> input('worker_id');

        $session_id = $this -> workstationService -> getAgentSession( $worker_id );

        $this -> workstationService -> progress( $worker_id, "1", 2);

        $params['redflag_id'] = ($id==1?0:$id);

        $params['session_id'] = $session_id;

        event( new AgentWorkstationRequestBroadcast($params) );

        return ['status' => 'OK' ];
    }
}
