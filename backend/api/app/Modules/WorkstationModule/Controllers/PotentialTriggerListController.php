<?php

namespace App\Modules\WorkstationModule\Controllers;

use Illuminate\Container\Container;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\WorkstationModule\Services\PotentialTriggerService;

class PotentialTriggerListController extends Controller {

  
    /** @var PotentialTriggerService $service */
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
        $this -> service = $container -> get('PotentialTriggerService');
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
        $conditions = array('column' => $request->query('column'), 'value' => $request->query('value'), 'search' => $request -> query('search'));
        return $this -> service ->query($conditions, $page);
    }

}
