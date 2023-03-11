<?php 

namespace App\Modules\Maintenance\Controllers;

use App\Modules\Maintenance\Services\MaintenanceService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Container\Container;

class UserNetTypeController extends Controller {

    /** @var MaintenanceService $service */
    protected $service;

    /*
     *
     * Constructor dependency method
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct(Container $container ){
        $this -> service = $container -> get('MaintenanceService');
    }

    /**
     *
     * Handles the list of MSA
     *
     * @param Request $request
     * @return type
     * @throws conditon
     **/
    public function __invoke(Request $request){

        $network_type = $this -> service -> getNetorkType($request -> query('ip'));

        return response()->json($network_type) ;
    }
}