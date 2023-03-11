<?php

namespace App\Modules\WorkstationModule\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Storage;
use Laravel\Lumen\Routing\Controller as BaseController ;

class AgentLogsSaveController extends BaseController {
    /** @var \App\Modules\WorkstationModule\Services\EventLogService  $service */
    protected $service;

    /**
     * Constructor Method
     * Define constructor dependencies here
     *
     * @return void
     **/
    public function __construct(Container $container ){
        $this -> service = $container -> get ('EventLogService');

    }


    /**
     * Handles the Application list
     *
     * @param Request $request
     * @return Response
     * @throws conditon
     **/
    public function __invoke( Request $request, $worker_id)
    {
        $args = $request -> only('filename', 'type', 'log');


        $this -> service -> setFilename( $args['filename'] )
            -> setType( $args['type'] )
            -> setWorkerID( $worker_id );

        $this -> service -> logEvent( $args['log'] );

        return [ 'status' => 'OK'];
    }


}
