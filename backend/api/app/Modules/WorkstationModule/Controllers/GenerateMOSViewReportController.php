<?php 

namespace App\Modules\WorkstationModule\Controllers;

use App\Modules\WorkstationModule\Services\AgentConnectionService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Container\Container;

class GenerateMOSViewReportController extends Controller {

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

        $breakdownField = $request -> input('breakdownField');

        $report_type = $request -> input('type');

        $search = $request -> query('search');

        $user = $this -> getUser( $request );

        $this -> service -> setUser( $user );

        $detailed_breakdown = [];

        $breakdown = $this -> service -> getBaseBreakdown() -> groupBy('account');
        
        foreach( $breakdown as $account => $item ){
            $country_breakdown = collect($item -> all()) -> groupBy('country');

            $detailed_breakdown[ $account ] = $country_breakdown;
        }

        $mosDataBreakdown = array(
            'account_stats' => $this -> service -> getConnectionStats(),
            'detailed_breakdown_stats' => $detailed_breakdown,
            'location_stats' => $this -> service -> getConnectionStatsBreakdown( 'location'),
            'country_stats' => $this -> service -> getConnectionStatsBreakdown( 'country'),
            'total_stats' => $this -> service -> getTotalStats()
        );

        return $this -> service -> generateExcelPdfReportFromMOSView($report_type, $search, $breakdownField, $mosDataBreakdown );
    }
}