<?php 

namespace App\Modules\WorkstationModule\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Container\Container;
use App\Modules\WorkstationModule\Services\AgentConnectionService;
use Illuminate\Support\Arr;


class AgentConnectionRecentUpdatesController extends Controller {

    /** @var AgentConnectionService $service */
    protected $service;

    /**
     *
     * Dependency constructor
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
     * Undocumented function long description
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __invoke(Request $request){
        $conditions = Arr::only($request -> query(), ['location','account', 'agent_name', 'country','ISP', 'VLAN', 'DNS_1', 'DNS_2', 'subnet', 'connection', 'speedtest'] );

        $sort = $request -> only('sortBy', 'sortOrder');

        if( $sort )
            $this -> service -> setSort( $sort['sortBy'], $sort['sortOrder']);


        $conditions['is_admin'] = false;

        $user = $this -> getUser( $request );
    
        $conditions = $this -> _parseLocation( $user, $conditions );

        $conditions = $this -> _parseAccount ($user, $conditions );
        
        return [ 'total' => $this -> service -> countConnections($conditions) ];
        // $server_time = $request -> query('server_time', false);
        // $ph_time = $request -> query('ph_time', false);
        // $has_timestamp = $server_time && $ph_time;
        // $cache_time = $this -> _generateCacheTime();
        // $count  = 0;

        // if( $has_timestamp ){
        //     $count = $this -> service -> countUpdatedConnections( $server_time, $ph_time );
        //     $cache_time = [
        //         'server_time' => $server_time,
        //         'ph_time' => $ph_time
        //     ];
        // }

        // return [ 
        //     'updates' => $count ,
        //     'cache_time' => $cache_time
        // ];
    }

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

     /**
     *
     * Generate cache time for server_time and ph_time
     * Need to centralize timer soon
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    private function _generateCacheTime(){
        $server_time = date("Y-m-d H:i:s");
        
        $ph_time     = date("Y-m-d H:i:s");

        return [
            'server_time' => $server_time,
            'ph_time' => $ph_time
        ];
    }
}