<?php

namespace App\Modules\HistoricalRecords\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HistoricalRecords\Services\MeanOpinionScoreRecordsService;
use Illuminate\Container\Container;
use Illuminate\Http\Request;

class MeanOpinionScoreController extends Controller {

    /** @var MeanOpinionScoreRecordsService $var description */
    protected $service;

    /**
     *
     * Constructor dependencies
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct(Container $container){
        $this -> service = $container -> get('MeanOpinionScoreRecordsService');
    }

    /**
     *
     * Handles fetching of MOS
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
