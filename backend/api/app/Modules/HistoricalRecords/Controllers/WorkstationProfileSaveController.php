<?php

namespace App\Modules\HistoricalRecords\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HistoricalRecords\Services\WorkstationProfileRecordsService;
use App\Modules\Applications\Services\ApplicationService;
use Illuminate\Container\Container;
use Illuminate\Http\Request;

class WorkstationProfileSaveController extends Controller {

    /** @var WorkstationProfileRecordsService $var description */
    protected $service;


    /** @var ApplicationService $var description */
    protected $applicationService;

    /**
     *
     * Constructor dependencies
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct(Container $container){
        $this -> service = $container -> get('WorkstationProfileRecordsService');

        $this -> applicationService = $container -> get('ApplicationService');

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
        $fields = $this -> service -> getFields();

        $params = $request -> only( $fields );

        $params['VLAN'] = $this -> applicationService -> _getVLAN( $params['subnet'] );

        $params['ISP'] = $this -> applicationService -> _getISPCode( $params['ISP']);

        $this -> service -> setWorkerID( $worker_id );

        $this -> service -> log( $params );

        return ['status' => 'OK', 'data' => $this -> service -> getModel() ];
    }
}
