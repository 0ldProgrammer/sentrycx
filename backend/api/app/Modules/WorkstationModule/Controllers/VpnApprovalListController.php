<?php

namespace App\Modules\WorkstationModule\Controllers;

use Illuminate\Container\Container;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\WorkstationModule\Services\WorkstationService;

class VpnApprovalListController extends Controller {

  
    /** @var WorkstationService $service */
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
        $this -> service = $container -> get('WorkstationService');
    }
    /**
     *
     * Handles the retrieval of vpn approval list
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/

    public function __invoke(Request $request){
        $page = $request -> query('page', 1);
        $status = $request -> query('status');
        $search = $request -> query('search');
        $perPage = $request -> query('perPage');

        return $this -> service -> getVpnApprovalData($page, $perPage, $status, $search);
    }

}
