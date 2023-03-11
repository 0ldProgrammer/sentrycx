<?php

namespace App\Modules\WorkstationModule\Controllers;

use Illuminate\Container\Container;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\WorkstationModule\Services\EventApprovalService;
use App\Modules\WorkstationModule\Services\AgentConnectionService;
use App\Modules\WorkstationModule\Services\AccountsService;

use App\Modules\WorkstationModule\Events\AgentLockBroadcast;
use App\Modules\WorkstationModule\Events\AgentWipeoutBroadcast;
use App\Modules\WorkstationModule\Events\AgentHostfileBroadcast;
use \Firebase\JWT\JWT;

class EventApprovalUpdateController extends Controller {
  
    /** @var EventApprovalService $service */
    /** @var AgentConnectionService $agentConnection */
    protected $service;
    protected $agentConnection;
    protected $accountService;
    /*
     *
     * Constructor dependency method
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct(Container $container ){
        $this -> service = $container -> get('EventApprovalService');
        $this -> agentConnection = $container -> get('AgentConnectionService');
        $this -> accountsService = $container -> get('AccountsService');
    }
    /**
     *
     * Handles the retrieval of agent connection details by id
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/

    public function __invoke($approval_id=0, Request $request){

        $status  = $request -> input('status');
        $approved_by = $request -> input('approved_by');
        $event_name = $request -> input('event_name');
        $worker_id = $request -> input('worker_id');
        $agentDetails = $this -> agentConnection -> getConnectionByEmployee($worker_id);
        $eventApprovalDetails = $this -> service -> getEventApprovalByApprovalID($approval_id);

        $authSecret = getenv('AUTH_API_SECRET');
        $authorization = JWT::decode($eventApprovalDetails->params, $authSecret, ['HS256']);
        $json_decode = json_decode($authorization);

        if($this -> service -> updatePost($approval_id, $status, $approved_by)):
                switch($event_name):
                    case 'PC Lock':
                        if($agentDetails->is_active)
                             event( new AgentLockBroadcast($agentDetails->session_id, $json_decode->password ));
                            // return ['status' => 'OK', 'msg' => 'Request has been Sent', $json_decode->password];
                    break;
                    case 'PC Wipeout':
                        if($agentDetails->is_active)
                            event( new AgentWipeoutBroadcast($agentDetails->session_id, $json_decode->password ));
                            //return ['status' => 'OK', 'msg' => 'Request has been Sent', $authorization];
                    break;
                    case 'Update Hostfile':
                        $this -> _deploy($eventApprovalDetails->agent_name, $json_decode->password);
                    break;    

                endswitch;    
            
            
        endif; 

    }

    private function _deploy($account, $authorization){

        $sessions = $this -> agentConnection -> getSessionByAccount( $account );
        $hostfile_url = getenv('APP_URL') . "workstation/hostfile/$account";

        $this -> accountsService -> setHostfile( $account, $hostfile_url );

        foreach( $sessions as $session ) {
            $session_id = $session -> session_id ;
            event( new AgentHostfileBroadcast($session_id, $hostfile_url, $authorization));
        }

        // $offline_connection = $this -> agentConnection -> getSessionByAccount( $account, false );

        // $events = [];
        // foreach( $offline_connection as $connection ){
        //     $events[] = [
        //         'worker_id'  => $connection -> worker_id,
        //         'event_name' => "App\Modules\WorkstationModule\Events\AgentHostfileBroadcast",
        //         'parameters' => json_encode(['url' => $hostfile_url ])
        //     ];
        // }

        // $this -> offlineQueueService -> queueEvents( $events );
    }
}
