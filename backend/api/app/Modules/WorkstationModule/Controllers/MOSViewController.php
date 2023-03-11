<?php 

namespace App\Modules\WorkstationModule\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Container\Container;
use App\Http\Controllers\Controller;
use App\Modules\WorkstationModule\Services\AgentConnectionService;

class MOSViewController extends Controller {
    
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
     * Handles the fetching of agent stats for MOS View
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __invoke(Request $request){
        $conditions = $request -> query('search');

        $user = $this -> getUser( $request );

        $this -> service -> setUser( $user );

        $detailed_breakdown = [];

        $breakdown = $this -> service -> getBaseBreakdown() -> groupBy('account');
        
        foreach( $breakdown as $account => $item ){
            $country_breakdown = collect($item -> all()) -> groupBy('country');

            $detailed_breakdown[ $account ] = $country_breakdown;
        }

        return [
            'base' => $this -> service -> getConnectionStats($conditions),
            'detailed_breakdown' => $detailed_breakdown,
            'location' => $this -> service -> getConnectionStatsBreakdown( 'location', $conditions),
            'country' => $this -> service -> getConnectionStatsBreakdown( 'country', $conditions),
            'total' => $this -> service -> getTotalStats($conditions)
        ];
    }


    private function _parseLocation( $user, $conditions ){
        $user_location = explode(',', $user -> location);   
  
      if( $user->location && !empty($conditions['ac.location'] ) ) {
          $conditions['ac.location'] = array_merge($conditions['ac.location'], $user_location);
      }
      
      else if( $user->location ) {
          $conditions['ac.location'] = $user_location;
      }
  
      return $conditions;
    }

}