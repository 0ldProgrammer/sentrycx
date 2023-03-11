<?php

namespace App\Modules\WorkstationModule\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Modules\Flags\Events\DesktopNotificationBroadcast;
use App\Http\Controllers\Controller as BaseController;
use App\Modules\WorkstationModule\Services\WorkstationService;
use Illuminate\Container\Container;

class AgentNotifyMediaStatsController extends BaseController{

    /** @var Type $clientURL description */
    protected $clientURL;

    /** @var WorkstationService $service  */
    protected $service;

    /**
     *
     * Dependency Constructor
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct( Container $container){
        $this -> service = $container -> get('WorkstationService');

        $this -> clientURL = getenv('CLIENT_URL');
    }

    /**
     *
     * Handles the sending of agent notification with the link
     * of media device checking
     *
     * @param Request $request
     * @param Integer $worker_id
     * @return Response
     * @throws conditon
     **/
    public function __invoke(Request $request, $worker_id = 0){

        $session_id = $this -> service -> getAgentSession( $worker_id );

        event( new DesktopNotificationBroadcast([
            'title'   => "Device Check",
            'message' => "Support Engineer wants you to check your media device. Click here to start with the test.",
            'session_id' => $session_id,
            'url'     => $this -> clientURL . "/client/device-check?workerID={$worker_id}"
        ]));

        return [ 'status' => 'OK', 'msg' => "Notified agent $worker_id on $session_id " ];
    }
}
