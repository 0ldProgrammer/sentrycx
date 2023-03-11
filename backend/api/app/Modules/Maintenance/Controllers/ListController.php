<?php

namespace App\Modules\Maintenance\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Container\Container;
use Laravel\Lumen\Routing\Controller as BaseController ;

class ListController extends BaseController {


    /** @var App\Modules\Applications\Services\ApplicationService  $applicationService description */
    protected $applicationService;
    /**
     * Constructor Method
     * Define constructor dependencies here
     *
     * @return void
     **/
    public function __construct(Container $container ){
        $this -> applicationService = $container -> get ('ApplicationService');
    }


    /**
     * Handles the Application list
     *
     * @param Request $request
     * @return Response
     * @throws conditon
     **/
    public function __invoke( Request $request, $account_id )
    {
        $data = $this -> applicationService -> getApplications($account_id);
        return ['data' => $data, 'status' => 'OK'];
    }
}
