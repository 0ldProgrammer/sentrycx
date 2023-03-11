<?php

namespace App\Modules\Flags\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Container\Container;

class CountryBreakdownController extends Controller {

    /** @var \App\Modules\Flags\Services\FlagService $service description */
    protected $flagService;

    /**
     * Constructor Method
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct(Container $container )
    {
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

        $user = $this->getUser($request);

        $filters = [];

        foreach( $fields as $field )
            $filters[ $field ] = $request -> query($field, null );

        return $this -> flagService -> accountOverview( $filters, 'location', $user );
        // return $this -> flagService -> accountOverview( $filters, 'country', $user );
        // return $this -> flagService -> accountOverview( $filters );
    }


}
