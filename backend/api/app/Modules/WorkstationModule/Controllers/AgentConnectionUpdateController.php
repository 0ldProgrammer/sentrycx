<?php

namespace App\Modules\WorkstationModule\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Container\Container;
use Laravel\Lumen\Routing\Controller as BaseController ;
use App\Modules\WorkstationModule\Events\AgentDashboardBroadcast;
use App\Modules\WorkstationModule\Events\AgentMTRUpdatedBroadcast;

class AgentConnectionUpdateController extends BaseController {
    /** @var App\Modules\WorkstationModule\Services\WorkstationService  $workstationService */
    protected $workstationService;
    /**
     * Constructor Method
     * Define constructor dependencies here
     *
     * @return void
     **/
    public function __construct(Container $container ){
        $this -> workstationService = $container -> get ('WorkstationService');
    }


    /**
     * Handles the Application list
     *
     * @param Request $request
     * @return Response
     * @throws conditon
     **/
    public function __invoke( Request $request)
    {
        $method = $request->method();

        $agent = $request->input();

        $data = $this -> workstationService -> sendMTR($agent, TRUE );

        $this -> _dispatchAgent( $agent );

        return ['data' => $data, 'status' => 'OK'];
    }

    private function _dispatchAgent( $agent ){
        event(new AgentDashboardBroadcast($agent));
        event(new AgentMTRUpdatedBroadcast($agent));
    }
}
