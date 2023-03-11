<?php 

namespace App\Modules\WorkstationModule\Controllers;

use App\Modules\WorkstationModule\Services\AgentConnectionService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Container\Container;

class GenerateApplicationsViewReportController extends Controller {

    /** @var AgentConnectionService $service */
    protected $service;

    /*
     *
     * Constructor dependency method
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct(Container $container ){
        $this -> service = $container -> get('AgentConnectionService');
    }

    /**
     *
     * 
     *
     * @param Request $request
     * @return type
     * @throws conditon
     **/
    public function __invoke(Request $request){

        $applications_type = $request -> input('appType');

        $report_type = $request -> input('type');
        $test = $this -> service -> getAgentAccounts($applications_type);

        $applications_breakdown = array(
            'accounts' => $this -> service -> getAgentAccounts($applications_type),
            'location' => $this -> service -> getLocationPerAccount($applications_type),
            'users' => $this -> service -> getUsersPerAccount($applications_type),
            'total' => $this -> service -> getTotalApplicationCounts($applications_type)
        );

        return $this -> service -> generateExcelPdfReportFromApplicationsView($report_type, $applications_type, $applications_breakdown );
    }
}