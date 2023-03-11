<?php

namespace App\Modules\Applications\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Container\Container;
use Laravel\Lumen\Routing\Controller as BaseController;

class GetIpController extends BaseController {

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
     * Handles the Code list
     *
     * @param Request $request
     * @return Response
     * @throws conditon
     **/
    public function __invoke( Request $request, $value, $table, $account)
    {
        $value = str_replace('*', '.', $value);
        $value = str_replace('+', '/', $value);
        $data = $this -> applicationService -> getIPURL($value, $table."s", $account);
        return ['status' => 'OK', 'data' => $data];
    }
}


