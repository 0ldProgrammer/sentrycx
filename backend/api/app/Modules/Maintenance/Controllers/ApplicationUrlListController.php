<?php
namespace App\Modules\Maintenance\Controllers;

use App\Modules\Maintenance\Services\MaintenanceService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Container\Container;
use Laravel\Lumen\Routing\Controller as BaseController;


class ApplicationUrlListController extends BaseController {

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
     * Handles the Application list
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
        return $this -> maintenanceService -> getApplications($page, $per_page,$search);
    }

    public function forDesktopList($account_id)
    {
        // $appDetails = $this -> maintenanceService -> getAppUrlDetailsFromRedis($account_id);
        // $all = json_decode(json_decode($appDetails) -> all);
        // $selected = json_decode(json_decode($appDetails) -> selected);
        $appDetails = $this -> maintenanceService -> getTopUrls($account_id);

        return response()->json(['all' => $appDetails]);
    }

    public function urlCounter(Request $request, $account_id)
    {
        $url = $request -> input('url');
        $this -> maintenanceService -> countUrl($url, $account_id);

                    
        return response()->json($url);
    }

    public function initializeRedisData()
    {
        $details = $this -> maintenanceService -> initializeApplicationURLsInRedis();
        foreach($details as $d)
        {
            $appUrls = $this -> maintenanceService -> getApplicationsUrlByAccountID($d -> account_id);
            $this -> maintenanceService -> storeAppUrlToRedis(strtolower($d -> account), $appUrls);

            print_r($appUrls);
            echo '<br />';
        }
    }

    public function resetUrlCounter()
    {
        if($this -> maintenanceService -> resetUrlCounter())
        {
            return response()->json(array('msg' => 'All counter is set to 0'));
        }
    }

}
