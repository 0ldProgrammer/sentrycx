<?php 

namespace App\Modules\HistoricalRecords\Controllers;

use App\Modules\HistoricalRecords\Services\PingTraceRecordsService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Container\Container;

class PingApplicationsListController extends Controller {

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
     * Handles the list of Ping Applications
     *
     * @param Request $request
     * @return type
     * @throws conditon
     **/
    public function __invoke(Request $request){
        $worker_id = $request -> input('workerID');
        return $this -> service -> fetchPingApplications($worker_id);
        return ['status' => 'OK', 'data' => $data];
    }
}