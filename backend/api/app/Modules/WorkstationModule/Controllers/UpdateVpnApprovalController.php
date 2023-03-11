<?php
namespace App\Modules\WorkstationModule\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Container\Container;
use App\Http\Controllers\Controller;
use App\Modules\WorkstationModule\Services\WorkstationService;
use App\Modules\WorkstationModule\Services\AgentConnectionService;
use App\Modules\WorkstationModule\Events\AgentVpnApprovalBroadcast;

class UpdateVpnApprovalController extends Controller {

    /** @var App\Modules\WorkstationModule\Services\WorkstationService  $workstationService description */
    protected $workstationService = null;

    /** @var AgentConnectionService $var description */
    protected $agentService;
    /**
     * Constructor Method
     * Define constructor dependencies here
     *
     * @return void
     **/
    public function __construct(Container $container ){
        $this -> workstationService = $container -> get ('WorkstationService');

        $this -> agentService = $container -> get('AgentConnectionService');
    }
    

    /**
     * Handles the vpn approval
     *
     * @param Request $request
     * @return Response
     * @throws conditon
     **/
    public function __invoke( Request $request)
    {
        $data = $request->input();

        $user = $this -> getUser( $request );
        
        $result = $this -> workstationService -> updateVpnApproval($data, $user->firstname);

        if ($result) {
        
            $this -> _dispatch($result->worker_id, $result);

            return ['status' => 'OK', 'data'=>  $result];
        }

    }

    /**
     *
     * Dispatch trigger to agent workstation
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function _dispatch($worker_id, $data ){
        $session_id = $this -> agentService -> getAgentSession( $worker_id );

        event( new AgentVpnApprovalBroadcast($session_id, $data ));

    }
}
