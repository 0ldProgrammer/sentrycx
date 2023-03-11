<?php 

namespace App\Modules\HistoricalRecords\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use App\Modules\HistoricalRecords\Services\SpeedtestRecordsService;

class SpeedtestController extends Controller {

    /** @var SpeedtestRecordsService $service */
    protected $service;

    /**
     *
     * Constructor Method
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct(Container $container){
        $this -> service = $container -> get('SpeedtestRecordsService');
    }

    /**
     *
     * Handles the fetching of speedtest history 
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __invoke(Request $request, $worker_id){
        $this -> service -> setWorkerID( $worker_id );
        return ['status' => 'OK', 'data' => $this -> service -> get() ];
    }
}