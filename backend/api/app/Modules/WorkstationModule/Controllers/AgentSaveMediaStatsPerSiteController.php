<?php

namespace App\Modules\WorkstationModule\Controllers;

use Illuminate\Container\Container;
use App\Modules\WorkstationModule\Services\MediaDeviceService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller as BaseController;

class AgentSaveMediaStatsPerSiteController extends BaseController {

    /** @var MediaDeviceService $service description */
    protected $service;
    /**
     *
     * Constructor Method
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct(Container $container){
        $this -> service = $container -> get('MediaDeviceService');
    }

    /**
     *
     * Handles the updating of media device stats per site
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __invoke(Request $request, $worker_id = 0 ){
        $args = [ 'remarks_per_sites' => $request -> input('remarks') ] ;

        $this -> service -> updateMediaDevice( $worker_id, $args, 'remarks_per_sites' );

        return [ 'status' => 'OK', 'msg' => 'Successfully updated.' ];
    }
}
