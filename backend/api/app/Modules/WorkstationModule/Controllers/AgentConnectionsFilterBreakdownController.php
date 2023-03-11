<?php

namespace App\Modules\WorkstationModule\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Container\Container;
use Illuminate\Support\Arr;
use App\Modules\WorkstationModule\Services\AgentConnectionService;

class AgentConnectionsFilterBreakdownController extends Controller {

    /** @var AgentConnectionService  $service */
    protected $service = null;

    /**
     * Constructor Dependencies
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct(Container $container)
    {
        $this -> service = $container -> get('AgentConnectionService');
    }

    /**
     * Handles the filter of agent connection
     *
     * @param Request $request
     * @return type
     * @throws conditon
     **/
    public function __invoke(Request $request ){
    
        $selected_filter = $request -> input('selectedFilter');

        $conditions = Arr::only($request -> query(), ['location','account', 'agent_name', 'country','ISP', 'VLAN', 'DNS_1', 'DNS_2', 'subnet', 'connection', 'speedtest', 'connection_type', 'net_type', 'position'] );

        $sort = $request -> only('sortBy', 'sortOrder');

        if( $sort )
            $this -> service -> setSort( $sort['sortBy'], $sort['sortOrder']);


        $conditions['is_admin'] = false;

        $user = $this -> getUser( $request );
    
        $conditions = $this -> _parseLocation( $user, $conditions );

        $conditions = $this -> _parseAccount ($user, $conditions );
        
        $data = $this -> service -> getFilterBreakdown($selected_filter, $conditions);

        return ['data' => $data];
    }


    // DEPRECATED : Use the parseLocation from the extended base controller ( Controller.php )
    private function _parseLocation( $user, $conditions ){
        $user_location = explode(',', $user -> location);   
  
      if( $user->location && !empty($conditions['location'] ) ) {
          $conditions['location'] = array_merge($conditions['location'], $user_location);
      }
      
      else if( $user->location ) {
          $conditions['location'] = $user_location;
      }
  
      return $conditions;
    }

    // DEPRECATED : Use the _parseAccount from the extended base controller ( Controller.php )
    private function _parseAccount( $user, $conditions ){
        $user_account_access = explode(',', $user -> account_access);   
  
      if( $user->account_access && !empty($conditions['account'] ) ) {
          $conditions['account'] = array_merge($conditions['account'], $user_account_access);
      }
      
      else if( $user->account_access ) {
          $conditions['account'] = $user_account_access;
      }
  
      return $conditions;
    }
    
}

