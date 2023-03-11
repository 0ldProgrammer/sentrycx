<?php


namespace App\Modules\Flags\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Container\Container;
use App\Modules\Flags\Services\FlagService;

class GenerateSummaryReportController extends Controller {
    /** @var FlagService  $flagService Instance of FlagService */
    private $flagService;
    /**
     * Constructor dependencies
     *
     * @param Container $continaer
     * @return void
     **/
    function __construct(Container $container){
        $this -> flagService = $container -> get('FlagService');
    }

    /**
     * Handles the API Request
     *
     * @param Request $request
     * @param Response $response
     * @return void
     **/
    function __invoke(Request $request){

        $report_type = $request -> input('type');

        $timezone = $request -> input('timezone');

        $headers = $request -> input('arrayColumns');

        $conditions = $request -> only(
            'account','agent_name','category_name', 'ISP', 
            'VLAN', 'DNS_1', 'DNS_2', 'subnet', 'location', 
            'status_info', 'category', 'code', 'connection', 'old_unresolved');

        $sort = $request -> only('sortBy', 'sortOrder');

        if( $sort )
            $this -> flagService -> setSort( $sort['sortBy'], $sort['sortOrder']);

        $user = $this -> getUser( $request );

        return $this -> flagService -> generateExcelPdfReportFromSummary($report_type, $user, $conditions, $headers, $timezone );
    }
}
