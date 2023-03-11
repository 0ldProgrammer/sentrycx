<?php
namespace App\Modules\Maintenance\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Container\Container;
use App\Http\Controllers\Controller;


class TimeFramesListController extends Controller {

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
     * Handles the Time Frames List
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
  
        $user = $this->getUser($request);
        $account_access = $this->parseAccount($user, null);
        $accounts = $account_access ? $account_access["account"]: [];
        return $this -> maintenanceService -> getTimeFrames($accounts, $page, $per_page, $search);
    }


}
