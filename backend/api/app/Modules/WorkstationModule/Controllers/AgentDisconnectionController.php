<?php

namespace App\Modules\WorkstationModule\Controllers;

use Illuminate\Container\Container;
use App\Http\Controllers\Controller;
use App\Modules\WorkstationModule\Events\AgentDisconnectedBroadcast;
use Illuminate\Http\Request;
use App\Modules\WorkstationModule\Services\AgentConnectionService;
use App\Modules\Applications\Services\DeviceStatusService;


class AgentDisconnectionController extends Controller {

    /** @var AgentConnectionService $service description */
    protected $workstationService;

    /**
     *
     * Constructor Dependencies
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct( Container $container ){
        $this -> service = $container -> get('AgentConnectionService');
        $this -> deviceStatusService = $container -> get('DeviceStatusService');
    }

    /**
     *
     * Handles the retrieval of agent connection details by id
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __invoke(Request $request){
        $session_id = $request -> query('session_id');

        $disconnected = $this -> service -> updateStatus($session_id, FALSE);

        if( $disconnected )
            $this -> deviceStatusService -> removeRedisData('DeviceStatus.'.$session_id);
            $this -> _dispatchDisconnect( $session_id );

        return [
            'status' => 'OK',
            'msg'    => "Agent $session_id disconnected",
            'data'   => $disconnected
        ];
    }

    private function _dispatchDisconnect( $session_id ){
        $data = ['session_id' => $session_id ];

        event(new AgentDisconnectedBroadcast($data));
    }
}
