<?php 

namespace App\Modules\WorkstationModule\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Container\Container;
use App\Modules\WorkstationModule\Services\AgentConnectionService;
use App\Modules\WorkstationModule\Events\AgentDashboardBroadcast;

class AuxStatusController extends Controller {
    /** @var AgentConnectionService service description */
    protected $service;

    /**
     *
     * Constructor dependencies
     *
     * @param Container $container Description
     * @return type
     * @throws conditon
     **/
    public function __construct(Container $container){
        $this -> service = $container -> get('AgentConnectionService');
    }

    /**
     *
     * Handles the endpoint for updating AUX Status
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __invoke(Request $request, $worker_id = 0 ){
        $status = $request -> input('status');

        $this -> service -> updateAuxStatus( $worker_id, $status );

        $this -> _dispatchAgent();

        return ['status' => 'OK', 'msg' => 'Status updated successfully'];
    }

    private function _dispatchAgent( ){
        event(new AgentDashboardBroadcast([]) );
    }
}