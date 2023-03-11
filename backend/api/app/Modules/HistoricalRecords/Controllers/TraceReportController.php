<?php 

namespace App\Modules\HistoricalRecords\Controllers;

use App\Modules\HistoricalRecords\Services\PingTraceRecordsService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Container\Container;

class TraceReportController extends Controller {

    /** @var PingTraceRecordsService $service */
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
        $this -> service = $container -> get('PingTraceRecordsService');
    }

    /**
     *
     * Handles trace report
     *
     * @param Request $request
     * @return type
     * @throws conditon
     **/
    public function __invoke(Request $request){
        $worker_id = $request -> input('workerID');
        $start_date = $request -> input('startingDate');
        $end_date = $request -> input('endingDate');
        $timezone = $request -> input('timezone');

        return $this -> service -> generateExcelReportFromTrace($worker_id, $start_date, $end_date, $timezone);
    }
}