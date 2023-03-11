<?php 
namespace App\Modules\WorkstationModule\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\WorkstationModule\Events\AgentAutoMOSBroadcast;
use Illuminate\Http\Request;
use Illuminate\Container\Container;
use App\Modules\WorkstationModule\Services\AgentConnectionService;

class AgentAutoMOSRequestController extends Controller {
    /** @var AgentConnectionService $service  */
    protected $service;

    const MESSAGE = [
        'STOP'  => "Automated sending of Mean Opinion Score(MOS) has been stopped. There will be no more updated MOS every 30 minutes until the agent restart the workstation",
        'START' => "Automated sending of Mean Opinion Score(MOS) has been started. There will be updated MOS every 30 minutes",
    ];

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
        $status     = $request -> get('status', 'STOP');
        event( new AgentAutoMOSBroadcast($session_id, $status));

        return ['status' => 'OK', 'msg' => self::MESSAGE[$status] ];
    }
}