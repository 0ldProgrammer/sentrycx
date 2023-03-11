<?php

namespace App\Modules\WorkstationModule\Controllers;

use Illuminate\Container\Container;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AgentConnectionDetailsController extends Controller {

    /** @var \App\Modules\WorkstationModule\Services\WorkstationService $workstationService description */
    protected $workstationService = null;

    /**
     *
     * Constructor Dependencies
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct( Container $container ){
        $this -> workstationService = $container -> get('WorkstationService');

        $this -> applicationService = $container -> get('ApplicationService');
    }

    /**
     *
     * Handles the retrieval of agent connection details by id
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __invoke(Request $request, $id = 0){
        $agent = $this -> workstationService -> getAgent( $id );
        $agent -> base = $this -> workstationService -> getWorkstation(['wp.worker_id' => $agent -> worker_id ]) -> last() ;


        return [
            'status' => 'OK',
            'data'   => $agent,
            'apps'   => $this -> applicationService -> getApplications( $agent -> account )
        ];
    }

}
