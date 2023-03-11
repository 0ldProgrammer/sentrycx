<?php
namespace App\Modules\Maintenance\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Container\Container;
use Laravel\Lumen\Routing\Controller as BaseController;


class SubnetMappingListController extends BaseController {

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
        $page = $request -> query('page');
        $per_page = $request -> query('per_page', 20);
        $search = $request -> query('search');
        return $this -> maintenanceService -> getMappingList($page, $per_page,$search);
        // $data = $this -> maintenanceService -> getAccountList();
        // return ['status' => 'OK', 'data' => $data];
    }


}
