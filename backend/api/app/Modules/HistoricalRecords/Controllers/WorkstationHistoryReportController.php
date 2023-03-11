<?php 

namespace App\Modules\HistoricalRecords\Controllers;

use App\Modules\HistoricalRecords\Services\WorkstationProfileRecordsService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Container\Container;

class WorkstationHistoryReportController extends Controller {

    /** @var WorkstationProfileRecordsService $service */
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
        $this -> service = $container -> get('WorkstationProfileRecordsService');
    }

    /**
     *
     * Handles workstation history report
     *
     * @param Request $request
     * @return type
     * @throws conditon
     **/
    public function __invoke(Request $request){
        try {

            $timezone = $request -> input('timezone');
            $worker_id = $request -> input('workerID');
            $start_date = $request -> input('startingDate');
            $end_date = $request -> input('endingDate');

            return $this -> service -> generateExcelReportFromWorkstationHistory($worker_id, $start_date, $end_date, $timezone);
    
        } catch (\Exception $e) {
            return response(["success" => "false"]);
        }
    }
}