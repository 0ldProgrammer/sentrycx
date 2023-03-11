<?php

namespace App\Modules\WorkstationModule\Controllers;

use Illuminate\Container\Container;
use App\Http\Controllers\Controller;
use App\Modules\WorkstationModule\Events\AgentLogsRequestBroadcast;
use Illuminate\Http\Request;

class AgentLogsController extends Controller {



    /**
     *
     * Constructor Dependencies
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct( Container $container ){
        $this -> service = $container -> get ('EventLogService');
    }

    /**
     *
     * Handles the retrieval of agent connection details by id
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __invoke($worker_id, Request $request ){
        return [
            'status' => 'OK',
            'data'   => $this -> service -> getLogs( $worker_id )
        ];
    }


}
