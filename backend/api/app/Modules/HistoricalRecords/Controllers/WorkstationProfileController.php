<?php 

namespace App\Modules\HistoricalRecords\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Container\Container;
use Illuminate\Http\Response;
use App\Modules\HistoricalRecords\Services\WorkstationProfileRecordsService;

class WorkstationProfileController extends Controller {

    /** @var WorkstationProfileRecordsService $service */
    protected $service;

    /**
     *
     * Constructor Dependencies
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct(Container $container){
        $this -> service = $container -> get('WorkstationProfileRecordsService');
    }

    /**
     *
     * Handles the endpoint for querying workstation profile records 
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __invoke(Request $request, $worker_id = 0){
        $page = $request -> query('page', 1);
        $this -> service -> setWorkerID( $worker_id );
        // return $this -> service -> query( $page, 100 );

        // return $this -> service -> queryRecent();
        return $this -> service -> get();
    }
}