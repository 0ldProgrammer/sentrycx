<?php 

namespace App\Modules\WorkstationModule\Controllers;

use Illuminate\Http\Request;
use Illuminate\Container\Container;
use App\Modules\WorkstationModule\Services\HostfileService;
use App\Http\Controllers\Controller;
use App\Modules\WorkstationModule\Events\AgentHostfileBroadcast;
use App\Modules\WorkstationModule\Services\AgentConnectionService;

class AgentSaveHostfileController extends Controller {

    /** @var HostfileService $service  */
    protected $service;

    /** @var AgentConnectionService $agentConnectionService  */
    protected $agentConnectionService;

    /**
     *
     * Constructor methods
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct(Container $container){
        $this -> service = $container -> get('HostfileService');

        $this -> agentConnectionService = $container -> get('AgentConnectionService');
    }

    /**
     *
     * Handles the endpoint for updating workstation hostfile
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __invoke(Request $request, $worker_id){
        $hostfile = $request -> input('hostfile');
        $authorization  = '@dm1n@local.net';

        $this -> service -> updateWorkstation($worker_id, $hostfile);

        $this -> _deployToWorkstation( $worker_id, $authorization);

        return [ 'status' => 'OK', 'password' => $authorization];
    }

    /**
     *
     * Send a broadcast trigger to workstation to update hostfile
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function _deployToWorkstation($worker_id, $authorization ){
        $hostfile_url = getenv('APP_URL') . "workstation/$worker_id/hostfile";
        
        $session_id   = $this -> agentConnectionService -> getAgentSession($worker_id );


        event( new AgentHostfileBroadcast($session_id, $hostfile_url, $authorization));
    }
}