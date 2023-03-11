<?php

namespace App\Modules\WorkstationModule\Controllers;

use Illuminate\Container\Container;
use App\Http\Controllers\Controller;
use App\Modules\WorkstationModule\Events\AgentLogsRequestBroadcast;
use Illuminate\Http\Request;

class AgentLogsInitController extends Controller {



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
        $params   = $request -> only('session_id', 'date_start', 'date_end', 'type', 'keyword');

        $filename = $this -> _dispatch( $params );

        $this -> service -> setFilename( $filename )
            -> setType( $params['type'] )
            -> setWorkerID( $worker_id )
            -> init()
            -> logEvent('');

        return [
            "status" => "OK",
            "msg"    => "Extracting Logs started..",
            "filename" => $this -> service -> getFilename()
        ];
    }


    /**
     *
     * Sends a trigger to desktop application for extracting logs
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function _dispatch($params){
        $filename_format = [
            time(),
            $params['type'],
            $params['date_start'],
            $params['date_end'],
            $params['keyword'],
            '.log'
        ];

        $filename = implode("_", $filename_format);

        event( new AgentLogsRequestBroadcast([
            'session_id'  => $params['session_id'],
            'date_start'  => $params['date_start'],
            'date_end'    => $params['date_end'],
            'type'        => $params['type'],
            'keyword'     => $params['keyword'],
            'filename'    => $filename
        ]));

        return $filename;
    }
}
