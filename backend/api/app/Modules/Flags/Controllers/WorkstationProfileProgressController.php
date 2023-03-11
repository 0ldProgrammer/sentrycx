<?php

namespace App\Modules\Flags\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class WorkstationProfileProgressController extends Controller {

    /** @var \App\Modules\WorkstationModule\Services\WorkstationService $workstationService  */
    protected $workstationService;

    /**
     *
     * Dependencies
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct(Container $container ){
        $this -> workstationService = $container -> get('WorkstationService');
    }

    /**
     *
     * Handles the route function
     *
     * @param Request $request
     * @param Int $id
     * @return Response
     * @throws conditon
     **/
    public function __invoke(Request $request, $worker_id = 0 ){
        $progress = $this -> workstationService -> getProgress( $worker_id, 2 );

        return ['progress' => $progress, 'worker_id' => $worker_id] ;
    }
}
