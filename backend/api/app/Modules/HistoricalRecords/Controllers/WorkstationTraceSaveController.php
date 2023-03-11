<?php 

namespace App\Modules\HistoricalRecords\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Modules\HistoricalRecords\Services\WorkstationTraceRecordsService;

class WorkstationTraceSaveController extends Controller {
    /** @var WorkstationTraceRecordsService $service  */
    protected $service;

    /**
     *
     * Dependency Constructor Injections
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct( Container $container ){
        $this -> service = $container -> get('WorkstationTraceRecordsService');
    }

    /**
     *
     * Handles the saving of workstation stack trace
     *
     * @param Request $request Description
     * @return Response
     * @throws conditon
     **/
    public function __invoke(Request $request, $worker_id = 0 ){
        $params    = $request -> only('stacktrace', 'timelog', 'workstation');

        $worker_id = $request -> input('worker_id', 0);

        $this -> service -> setWorkerID( $worker_id );

        $this -> service -> log($params);

        return [ 'status' => 'OK' ];
    }
}