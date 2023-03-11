<?php
namespace App\Modules\Applications\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Container\Container;
use Laravel\Lumen\Routing\Controller as BaseController;
use App\Modules\HistoricalRecords\Services\SecureCXRecordsService;


class StartUpCheckerController extends BaseController {

    /** @var App\Modules\Applications\Services\ApplicationService  $applicationService description */
    protected $applicationService;

    protected $secureCXMonitoring;
    /**
     * Constructor Method
     * Define constructor dependencies here
     *
     * @return void
     **/
    public function __construct(Container $container ){
        $this -> applicationService = $container -> get ('ApplicationService');
        $this -> secureCXMonitoring = $container -> get ('SecureCXRecordsService');
    }


    /**
     * Handles the Code list
     *
     * @param Request $request
     * @return Response
     * @throws conditon
     **/
    public function __invoke( Request $request, $account)
    {
        $data = $this -> applicationService -> StartUpRequirementsPerAccount($account);
        $securecx_url = $this -> secureCXMonitoring -> fetchUrls();
        return ['status' => 'OK', 'data' => $data, 'securecx_url' => $securecx_url];
    }
}
