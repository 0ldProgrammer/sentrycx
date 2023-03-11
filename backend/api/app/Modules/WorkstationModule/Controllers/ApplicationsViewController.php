<?php 

namespace App\Modules\WorkstationModule\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Container\Container;
use App\Http\Controllers\Controller;
use App\Modules\WorkstationModule\Services\AgentConnectionService;

class ApplicationsViewController extends Controller {
    
    /** @var AgentConnectionService $service */
    protected $service;

    /**
     *
     * Constructor dependencies
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct(Container $container ){
        $this -> service = $container -> get('AgentConnectionService');
    }

    /**
     *
     * Handles the fetching of agent applications View
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __invoke(Request $request){

        $applications_type = $request -> input('applicationsType');

        return [
            'accounts' => $this -> service -> getAgentAccounts($applications_type),
            'location' => $this -> service -> getLocationPerAccount($applications_type),
            'total' => $this -> service -> getTotalApplicationCounts($applications_type)
        ];
    }
}