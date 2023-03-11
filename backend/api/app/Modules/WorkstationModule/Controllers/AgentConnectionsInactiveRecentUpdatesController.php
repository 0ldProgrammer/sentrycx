<?php 

namespace App\Modules\WorkstationModule\Controllers;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Container\Container;
use App\Http\Controllers\Controller;
use App\Modules\WorkstationModule\Services\AgentConnectionService;

class AgentConnectionsInactiveRecentUpdatesController extends Controller {
    /** @var AgentConnectionService $service */
    protected $service;

    /**
     *
     * Dependency Constructor Injections 
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct( Container $container ){
        $this -> service = $container -> get('AgentConnectionService');
    }
    
    /**
     *
     * Handles the /workstation/inactive [GET] endpoint
     *
     * @param Request $request 
     * @return Response
     * @throws conditon
     **/
    public function __invoke(Request $request){
        $page = $request -> query('page', 1);

        $conditions = Arr::only($request -> query(), ['location','account', 'agent_name', 'country','ISP', 'VLAN', 'DNS_1', 'DNS_2', 'subnet', 'speedtest'] );

        $conditions['connection'] = 'offline';

        $sort = $request -> only('sortBy', 'sortOrder');

        if( $sort )
            $this -> service -> setSort( $sort['sortBy'], $sort['sortOrder']);


        $conditions['is_admin'] = false;

        $user = $this -> getUser( $request );
    
        $conditions = $this -> parseLocation( $user, $conditions );

        $conditions = $this -> parseAccount ($user, $conditions );
        
        return [ 'total' => $this -> service -> countConnections($conditions) ];
    }
}