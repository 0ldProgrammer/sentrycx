<?php 

namespace App\Modules\HistoricalRecords\Controllers;

use App\Modules\HistoricalRecords\Services\LogsRecordsService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Container\Container;

class LogsReportController extends Controller {

    /** @var LogsRecordsService $service */
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
        $this -> service = $container -> get('LogsRecordsService');
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
        $worker_id = $request -> input('workerID');
        $time = $request -> input('time');
        $date_time = $request -> input('date_time');

        $getLabels = $this -> service -> getLabels($time, $date_time);

        $logs = $this -> service -> getHistoricalLogs($worker_id, $time, $date_time);

        return [
            'labels' => $getLabels,
            'logs' => $logs
        ];
    }
}