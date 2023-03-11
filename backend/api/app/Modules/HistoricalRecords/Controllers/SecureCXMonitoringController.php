<?php

namespace App\Modules\HistoricalRecords\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HistoricalRecords\Services\SecureCXRecordsService;
use Illuminate\Container\Container;
use Illuminate\Http\Request;

class SecureCXMonitoringController extends Controller {

    /** @var SecureCXRecordsService $var description */
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
        $this -> service = $container -> get('SecureCXRecordsService');
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
        $url = $request->input('url');
        $this -> service -> setWorkerIDToSecureCX( $worker_id );
        return ['status' => 'OK', 'data' => $this -> service -> getSecureCXMonitoring($url) ];
    }
}
