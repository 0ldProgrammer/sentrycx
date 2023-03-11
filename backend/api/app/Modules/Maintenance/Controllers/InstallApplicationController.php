<?php
namespace App\Modules\Maintenance\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Container\Container;
use Laravel\Lumen\Routing\Controller as BaseController;
use App\Modules\Maintenance\Events\AgentLoginDeploymentApplicationBroadcast;

class InstallApplicationController extends BaseController {

    /** @var App\Modules\Maintenance\Services\MaintenanceService  $maintenanceService description */
    protected $maintenanceService;

    /**
     * Constructor Method
     * Define constructor dependencies here
     *
     * @return void
     **/
    public function __construct(Container $container ){
        $this -> maintenanceService = $container -> get('MaintenanceService');
    }
    

    /**
     * Handles the adding of application
     *
     * @param Request $request
     * @return Response
     * @throws conditon
     **/
    public function __invoke( Request $request)
    {
        $worker_id = $request -> route('worker_id');
        
        $data = $this -> maintenanceService -> getApplicationsInstaller();

        if ($data) {
            $this -> _dispatch($data, $worker_id);

            return ['status' => 'OK', 'data'=>  $data];
        }

    }

    public function _dispatch($data, $worker_id){

        $session_id = $this -> maintenanceService -> getAgentSession($worker_id);
            
        event( new AgentLoginDeploymentApplicationBroadcast($session_id, $data ));
        
    }

}
