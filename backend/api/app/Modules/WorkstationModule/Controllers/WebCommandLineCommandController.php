<?php 

namespace App\Modules\WorkstationModule\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Container\Container;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Modules\WorkstationModule\Services\CommandLineService;
use App\Modules\WorkstationModule\Events\AgentCommandLineBroadcast;
use App\Modules\WorkstationModule\Services\AgentConnectionService;

class WebCommandLineCommandController extends Controller {

    /** @var CommandLineService $service  */
    protected $service;

    /** @var AgentConnectionService $var description */
    protected $agentService;

    /**
     *
     * Constructor dependencies
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct(Container $container){
        $this -> service = $container -> get('CommandLineService');

        $this -> agentService = $container -> get('AgentConnectionService');
    }

    /**
     *
     * Triggers the command to the agent workstation
     *
     * @param Response $var Description
     * @return type
     * @throws conditon
     **/
    public function __invoke(Request $request, $worker_id = 0) {
        $command = $request -> input('command');

        $type = $request -> input('type', 'CMD');

        $this -> service -> setWorkerID( $worker_id );

        $data = $this -> service -> logCommand( $command, $type );

        $this -> _dispatch( $worker_id, $data);

        return ['status' => 'OK', 'data'=>  $data];
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
       
        //event( new AgentCommandLineBroadcast($session_id, $data ));

    }
}