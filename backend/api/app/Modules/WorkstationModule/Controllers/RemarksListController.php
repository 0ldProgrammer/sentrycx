<?php
namespace App\Modules\WorkstationModule\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Container\Container;
use Laravel\Lumen\Routing\Controller as BaseController;


class RemarksListController extends BaseController {

    /** @var App\Modules\WorkstationModule\Services\WorkstationService  $workstationService description */
    protected $workstationService;
    /**
     * Constructor Method
     * Define constructor dependencies here
     *
     * @return void
     **/
    public function __construct(Container $container ){
        $this -> workstationService = $container -> get ('WorkstationService');
    }
    

    /**
     * Handles the Remarks Data
     *
     * @param Request $request
     * @return Response
     * @throws conditon
     **/
    public function __invoke( Request $request)
    {
        $data = $this -> workstationService -> getRemarksData();

        return ['status' => 'OK', 'data' => $data];
    }
}
