<?php
namespace App\Modules\Maintenance\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Container\Container;
use Laravel\Lumen\Routing\Controller as BaseController;


class AgentThemeController extends BaseController {

    /** @var App\Modules\Maintenance\Services\MaintenanceService  $maintenanceService description */
    protected $maintenanceService;
    /**
     * Constructor Method
     * Define constructor dependencies here
     *
     * @return void
     **/
    public function __construct(Container $container ){
        $this -> maintenanceService = $container -> get ('MaintenanceService');
    }
    

    /**
     * Handles the Code list
     *
     * @param Request $request
     * @return Response
     * @throws conditon
     **/
    public function __invoke( Request $request)
    {
        if($this -> maintenanceService -> saveAndUpdateAgentTheme($request->input())){
            return response()->json(array('status' => true, 'msg' => 'Saved Succesfully'));
        }else{
            return response()->json(array('status' => false, 'msg' => 'Sorry, something went wrong'));
        }
        
    }

    public function getAgentTheme($worker_id)
    {
        $themeDetails = $this -> maintenanceService -> getAgentTheme($worker_id);

        return response()->json($themeDetails);
    }
}
