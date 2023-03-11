<?php

namespace App\Modules\WorkstationModule\Controllers;

use Illuminate\Container\Container;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\WorkstationModule\Services\EventApprovalService;

class EventApprovalListController extends Controller {
  
    /** @var EventApprovalService $service */
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
        $this -> service = $container -> get('EventApprovalService');
    }
    /**
     *
     * Handles the retrieval of agent connection details by id
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/

    public function __invoke(Request $request){
        $page = $request -> query('page', 1);
        $conditions = $request -> query('search');
        return $this -> service ->query($conditions, $page);
    }

}
