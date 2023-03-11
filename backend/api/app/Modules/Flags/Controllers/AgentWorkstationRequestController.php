<?php

namespace App\Modules\Flags\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request;
use App\Modules\Flags\Events\AgentWorkstationRequestBroadcast;

class AgentWorkstationRequestController extends Controller {
    /**
     *
     * Defines dependencies
     *
     * @return void
     * @throws conditon
     **/
    public function __construct(){}

    /**
     *
     * Handles the routing function
     *
     * @param Type $var Descriptiona
     * @return type
     * @throws conditon
     **/
    public function __invoke(Request $request)
    {
        # code...
    }
}
