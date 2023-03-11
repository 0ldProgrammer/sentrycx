<?php

namespace App\Modules\WorkstationModule\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Container\Container;
use Illuminate\Support\Arr;
use App\Modules\WorkstationModule\Services\AgentConnectionService;

class GenerateConnectedTOCReportController extends Controller {
    /** @var AgentConnectionService $service  */
    protected $service;

    /**
     * Constructor Dependencies
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct(Container $container){
        $this -> service = $container -> get('AgentConnectionService');
    }

    /**
     * Handles the list of agent connections
     *
     * @param Request $request
     * @return type
     * @throws conditon
     **/
    public function __invoke(Request $request ){
        $headers = $request -> input('arrayColumns');

        $timezone = $request -> input('timezone');

        $report_type = $request -> input('type');

        $conditions = Arr::only($request -> query(), ['location','account', 'agent_name', 'country', 'aux_status'] );

        return $this -> service -> generateExcelPdfReportFromConnectedTOC($report_type, $conditions, $headers, $timezone);
    }
}
