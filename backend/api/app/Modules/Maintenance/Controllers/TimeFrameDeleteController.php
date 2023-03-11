<?php
namespace App\Modules\Maintenance\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Container\Container;
use Laravel\Lumen\Routing\Controller as BaseController;


class TimeFrameDeleteController extends BaseController {

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
     * Handles the deleting of time frame
     *
     * @param Request $request
     * @return Response
     * @throws conditon
     **/
    public function __invoke( Request $request)
    {
        $this -> maintenanceService -> deleteTimeFrame($request->input('id'));

        return ['status' => 'OK'];
    }
}
