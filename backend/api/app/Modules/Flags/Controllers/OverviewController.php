<?php

namespace App\Modules\Flags\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Container\Container;
use \App\Modules\Flags\Services\FlagService;

class OverviewController extends Controller {
    /** @var FlagService $flagService FlagService instance */
    private $flagService;

    /**
     * Constructor Dependencies
     *
     *
     * @param Container $container
     * @return void
     * @throws conditon
     **/
    public function __construct(Container $container){
        $this -> flagService = $container -> get('FlagService');
    }


    /**
     * Handles the page paired on the route
     *
     * @param Request $request
     * @return Response
     * @throws conditon
     **/
    public function __invoke(Request $request){
        
        $fields = $request -> query('fields', [] );

        $user = $this -> getUser( $request );
        
        $filters = [];

        foreach( $fields as $field )
            $filters[ $field ] = $request -> query($field, null );

        // echo '<pre>';
        // print_r($filters);
        // die();
        $base = $this -> flagService -> accountOverview( $filters, null, $user );
        $country_breakdown = $this -> flagService -> accountOverview( $filters, 'country', $user );

        return [
            'base' => $base,
            'country' => collect( $country_breakdown ) -> groupBy('account'),
            'total'   => $this -> flagService -> totalCount( $filters, $user )
        ];
    }
}
