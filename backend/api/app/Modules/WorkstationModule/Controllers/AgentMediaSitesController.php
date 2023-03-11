<?php

namespace App\Modules\WorkstationModule\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Container\Container;
use App\Http\Controllers\Controller as BaseController;
use App\Modules\WorkstationModule\Services\MediaDeviceService;
use App\Modules\WorkstationModule\Services\AgentConnectionService;


class AgentMediaSitesController extends BaseController {

    /** @var MediaDeviceService $mediaDeviceService */
    protected $mediaDeviceService;

    /** @var AgentConnectionService $agentConnectionService */
    protected $agentConnectionService;

    /**
     *
     * Constructor Dependencies
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct(Container $container){
        $this -> mediaDeviceService = $container -> get('MediaDeviceService');

        $this -> agentConnectionService  = $container -> get('AgentConnectionService');
    }

    /**
     *
     * Handles the retrieval of list of sites
     * needs to be checked per account
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __invoke(Request $request, $worker_id ){
        // $account = 'OPEXOTHER';
        $agent = $this -> agentConnectionService -> getConnectionByEmployee( $worker_id );

        if( !$agent )
            return ['status' => 'OK', 'data' => [], 'msg' => 'Unknow agent.' ];

        return [
            'status' => 'OK',
            'data'   => $this -> mediaDeviceService -> getMediaSites( $agent -> account )
        ];
    }

}
