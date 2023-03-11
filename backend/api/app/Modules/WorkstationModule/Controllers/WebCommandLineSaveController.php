<?php 

namespace App\Modules\WorkstationModule\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\WorkstationModule\Events\DashboardCommandLineBroadcast;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Modules\WorkstationModule\Services\CommandLineService;
use App\Modules\WorkstationModule\Services\AgentConnectionService;

class WebCommandLineSaveController extends Controller {

    /** @var CommandLineService $service  */
    protected $service;

    /** @var AgentConnectionService $agentService*/
    protected $agentService;

    /**
     *
     * Dependency injections
     *
     * @param Container $container
     * @return null
     * @throws conditon
     **/
    public function __construct(Container $container){
        $this -> service = $container -> get('CommandLineService');
        $this -> agentService = $container -> get('AgentConnectionService');
    }

    /**
     *
     * Handles the saving of cmd/powershell return
     *
     * @param Request $request
     * @param String $worker_id
     * @return type
     * @throws conditon
     **/
    public function __invoke(Request $request, $worker_id = 0, $id = 0 ) {
        $response = $request -> input('response');
        $data = $this -> service -> logResponse( $id, $response );

        $this -> _dispatch( $worker_id, $data);
        return [
            'status' => 'OK', 
            'data' => $data
        ];
    }

    private function _dispatch($worker_id, $data){
        $session_id = $this -> agentService -> getAgentSession($worker_id);
        event( new DashboardCommandLineBroadcast($session_id, $data));
    }
}