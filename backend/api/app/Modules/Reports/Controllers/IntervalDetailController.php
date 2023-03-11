<?php 

namespace App\Modules\Reports\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Container\Container;
use App\Modules\Reports\Services\ConnectionReportService;
use App\Modules\Reports\Services\ReportService;

class IntervalDetailController extends Controller {

    /** @var ConnectionReportService $service */
    protected $service;

    /** @var ReportService $reportService */
    protected $reportService;

    /** @var Array $fields */
    protected $fields = [
        'logged_time',
        'agent_name',
        'agent_email',
        'worker_id',
        'station_name',
        'location',
        'account',
        'country',
        'connection_type',
        'net_type',
        'job_profile',
        'lob',
        'msa_client',
        'programme_msa',
        'supervisor_email_id',
        'supervisor_full_name',
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

        $data = $this -> service -> query( $date_start, $date_end );

        return $this -> reportService -> exportToExcel( $data, $this -> fields );
    }
}