<?php 
namespace App\Modules\WorkstationModule\Controllers;

use App\Http\Controllers\Controller;
// use App\Modules\WorkstationModule\Events\AgentAutoMOSBroadcast;
use App\Modules\WorkstationModule\Events\AgentMOSRequestBroadcast;
use Illuminate\Http\Request;
use Illuminate\Container\Container;
use App\Modules\WorkstationModule\Services\AgentConnectionService;

class AgentMOSRequestController extends Controller {
    /** @var AgentConnectionService $service  */
    protected $service;

    /**
     *
     * Constructor dependencies
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct( Container $container ){
        $this -> service = $container -> get('AgentConnectionService');
    }

    /**
     * 
     * Handles sending of AutoMOS request trigger
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __invoke(Request $request, $worker_id ){
        $session_id = $this -> service -> getAgentSession( $worker_id );

        event( new AgentMOSRequestBroadcast($session_id));

        return ['status' => 'OK', 'msg' => "Agent's Mean Opinion Score(MOS) will be updated shortly. Please try to open the popup in a few seconds."];
    }
}