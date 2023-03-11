<?php

namespace App\Modules\Zoho\Controllers;

use Illuminate\Http\Request;
use Illuminate\Container\Container;
use App\Http\Controllers\Controller as BaseController;

class ReportController extends BaseController {

    /** @var \App\Modules\Zoho\Services\ZohoLogService $var description */
    protected $service;

    /**
     *
     * Constructor Methods
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct( Container $container ){
        $this -> service = $container -> get('ZohoLogService');
    }

    /**
     *
     * Handles the fetching of reports based on workerID
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __invoke($worker_id = 0, Request $request ){
        return [
            'status' => 'OK',
            'data'   => $this -> service -> getByWorkerID( $worker_id )
        ];
    }
}
