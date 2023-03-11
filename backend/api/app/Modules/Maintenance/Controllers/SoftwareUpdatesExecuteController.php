<?php
namespace App\Modules\Maintenance\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Container\Container;
use App\Modules\Maintenance\Events\AgentSoftwareUpdateBroadcast;
use Laravel\Lumen\Routing\Controller as BaseController;


class SoftwareUpdatesExecuteController extends BaseController {

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
     * Handles the Software Updates list
     *
     * @param Request $request
     * @return Response
     * @throws conditon
     **/
    public function __invoke( Request $request)
    {
        $update_id = $request->input('update_id');
        $patch_name = $request->input('patch_name');

        $result = $this -> maintenanceService -> executeSoftwareUpdate($update_id, $patch_name);

        if ($result) {
        
            $this -> _dispatch($result, $update_id, $patch_name);

            return ['status' => 'OK'];
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
    public function _dispatch($data, $update_id, $patch_name){

        foreach($data as $value) {
            event( new AgentSoftwareUpdateBroadcast($value['session_id'], $update_id, $patch_name ));
        }
    }


}
