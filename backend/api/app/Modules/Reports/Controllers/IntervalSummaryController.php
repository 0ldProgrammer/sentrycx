<?php 

namespace App\Modules\Reports\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Container\Container;
use App\Modules\Reports\Services\ReportService;
use App\Modules\Reports\Services\ConnectionReportService;

class IntervalSummaryController extends Controller {

    /** @var ConnectionReportService $service */
    protected $service;

    /** @var ReportService $reportService */
    protected $reportService;

    /** @var Array $fields  */
    protected $fields = [
        'logged_time',
        'connected',
        'wired',
        'wireless',
        'wah',
        'bm',
        'vpn'
    ];

    /**
     *
     * Constructor method
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct(Container $container){
        $this -> service = $container -> get('ConnectionReportService');
        $this -> reportService = $container -> get('ReportService');
    }

    /**
     *
     * Handles the excel generation of 30 mins interval summary
     *
     * @param Request $request
     * @return type
     * @throws conditon
     **/
    public function __invoke(Request $request){
        $date_start = $request -> query('date_start', date('Y-m-d 00:00:00'));
        $date_end = $request -> query('date_end', date('Y-m-d 23:59:59'));

        $data = $this -> service -> summary( $date_start, $date_end );

        return $this -> reportService -> exportToExcel( $data, $this -> fields );
    }
}