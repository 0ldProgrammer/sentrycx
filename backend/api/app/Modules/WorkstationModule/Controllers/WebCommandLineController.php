<?php 


namespace App\Modules\WorkstationModule\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Container\Container;
use App\Modules\WorkstationModule\Services\CommandLineService;

class WebCommandLineController extends Controller {

    /** @var CommandLineService $service  */
    protected $service;

    /**
     *
     * Constructor dependencies
     *
     * @param Container $container
     * @return null
     * @throws conditon
     **/
    public function __construct(Container $container ){
        $this -> service = $container -> get('CommandLineService');
    }

    /**
     *
     * Handles fetching the logs 
     *
     * @param Request $request
     * @return Response
     * @throws conditon
     **/
    public function __invoke(Request $request, $worker_id = 0) {
        $this -> service -> setWorkerID( $worker_id );

        return [
            'status' => 'OK', 
            'powershell' => $this -> service -> getLogs('POWERSHELL'),
            'cmd' => $this -> service -> getLogs('CMD') 
        ];
    }
}