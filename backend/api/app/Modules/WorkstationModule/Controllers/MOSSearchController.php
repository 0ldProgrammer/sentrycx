<?php 

namespace App\Modules\WorkstationModule\Controllers;

use App\Modules\WorkstationModule\Services\AgentConnectionService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Container\Container;

class MOSSearchController extends Controller {

    /** @var AgentConnectionService $service */
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
        $this -> service = $container -> get('AgentConnectionService');
    }

    /**
     *
     * Handles the list of Accounts API
     *
     * @param Request $request
     * @return type
     * @throws conditon
     **/
    public function __invoke(Request $request){
        $conditions = $request -> query('search');
        $user = $this -> getUser( $request );

        $this -> service -> setUser( $user );
        
        $base = $this -> service -> getConnectionStats( $conditions );
        $location   = $this -> service -> getConnectionStatsBreakdown( 'location', $conditions );
        $country    = $this -> service -> getConnectionStatsBreakdown( 'country', $conditions );

        $detailed_breakdown = [];

        $breakdown = $this -> service -> getBaseBreakdown() -> groupBy('account');
        
        foreach( $breakdown as $account => $item ){
            $country_breakdown = collect($item -> all()) -> groupBy('country');

            $detailed_breakdown[ $account ] = $country_breakdown;
        }

        return [
            'base' => $base,
            'detailed_breakdown' => $detailed_breakdown,
            'location' => $location,
            'country' => $country,
            'total' => $this -> service -> getTotalStats($conditions)
        ];
    }
}