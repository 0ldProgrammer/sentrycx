<?php 

namespace App\Modules\HistoricalRecords\Controllers;

use App\Modules\HistoricalRecords\Services\MeanOpinionScoreRecordsService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Container\Container;

class MOSReportController extends Controller {

    /** @var MeanOpinionScoreRecordsService $service */
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
        $this -> service = $container -> get('MeanOpinionScoreRecordsService');
    }

    /**
     *
     * Handles mos report
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

        return $this -> service -> generateExcelReportFromMOS($worker_id, $start_date, $end_date, $timezone);
    }
}