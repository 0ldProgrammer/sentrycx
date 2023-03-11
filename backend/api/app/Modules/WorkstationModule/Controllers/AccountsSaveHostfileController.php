<?php 

namespace App\Modules\WorkstationModule\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Container\Container;
use App\Modules\WorkstationModule\Services\HostfileService;
use App\Modules\WorkstationModule\Services\AgentConnectionService;
use App\Modules\WorkstationModule\Events\AgentHostfileBroadcast;
use App\Modules\WorkstationModule\Services\OfflineQueueService;
use App\Modules\WorkstationModule\Services\AccountsService;
use App\Modules\WorkstationModule\Services\EventApprovalService;

use \Firebase\JWT\JWT;

class AccountsSaveHostfileController {

    /** @var HostfileService $service description */
    protected $service;

    /** @var AgentConnectionService $agentConnectionService  */
    protected $agentConnectionService;

    /** @var OfflineQueueService $offlineQueueService description */
    protected $offlineQueueService;

    /** @var AccountsService $accountsService description */
    protected $accountsService;

    /** @var EventApprovalService $accountsService description */
    protected $eventApprovalService;


    /**
     *
     * Undocumented function long description
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct(Container $container ){
        $this -> service = $container -> get('HostfileService');

        $this -> agentConnectionService = $container -> get('AgentConnectionService');

        $this -> offlineQueueService = $container -> get('OfflineQueueService');

        $this -> accountsService = $container -> get('AccountsService');

        $this -> eventApprovalService = $container -> get('EventApprovalService');
    }

    /**
     *
     * Handles saving of hostfile
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __invoke( $account = '', Request $request ){
        $deploy = $request -> input('deploy', FALSE);
        $hostfile = $request -> input('hostfileText');
        $requested_by = $request -> input('requested_by');
        $event = $request -> input('event');
        $agent_name = $request -> input('agent_name');
        $authorization = '@dm1n@local.net';

        $authSecret = getenv('AUTH_API_SECRET');
        $access_token = JWT::encode($authorization, $authSecret );

        $this -> service -> save( $account . ".swp", $hostfile );

        if($this -> eventApprovalService -> insert($requested_by, $event, $agent_name, $access_token, 0)):
            
            return ['status' => 'OK', 'msg' => 'Request has been Sent'];
            
        endif; 

        //return ['deploy' => $deploy, 'hostfile' => $hostfile];

        // $this -> service -> save( $account . ".swp", $hostfile );

        if( $deploy ){
            $this -> _deploy($account, $hostfile, $authorization);
        }
            

        // return [ 'status' => 'OK' ];
    }

    /**
     * Sends a trigger to update hostfile 
     * TODO : Convert this into OnlineJob
            : This can be optimized if run via job
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    private function _deploy($account, $hostfile, $authorization){
        $this -> service -> save( $account, $hostfile );

        $sessions = $this -> agentConnectionService -> getSessionByAccount( $account );
        $hostfile_url = getenv('APP_URL') . "workstation/hostfile/$account";

        $this -> accountsService -> setHostfile( $account, $hostfile_url );

        foreach( $sessions as $session ) {
            $session_id = $session -> session_id ;

            event( new AgentHostfileBroadcast($session_id, $hostfile_url, $authorization));
        }

        $offline_connection = $this -> agentConnectionService -> getSessionByAccount( $account, false );

        $events = [];
        foreach( $offline_connection as $connection ){
            $events[] = [
                'worker_id'  => $connection -> worker_id,
                'event_name' => "App\Modules\WorkstationModule\Events\AgentHostfileBroadcast",
                'parameters' => json_encode(['url' => $hostfile_url ])
            ];
        }

        $this -> offlineQueueService -> queueEvents( $events );
    }
}
