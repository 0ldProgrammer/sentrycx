<?php

namespace App\Modules\Flags\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Container\Container;

class FilterController extends Controller {
    /** @var \App\Modules\Flags\Services\FlagService $flagService */
    private $flagService;

    /**
     * Constructor dependencies
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct( Container $container ) {
        $this -> flagService = $container -> get('FlagService');
    }


    /**
     * Handles the route function
     *
     * @param Request $request
     * @return type
     * @throws conditon
     **/
    public function __invoke(Request $request ){
        // $filter_options = $request -> only('field', 'table');
        $fields = $request -> query('fields', false);
        $table = $request -> query('table', 'redflag_dashboard');

        return [
            'data'    => $this -> flagService -> getFilters( $fields, $table ),
            'issues'  => $this -> flagService -> getFilters(['ISP', 'VLAN', 'DNS_1', 'DNS_2', 'subnet'], 'workstation_profile'),
            'redflag' => $this -> flagService -> getFilters(['ISP', 'VLAN', 'DNS_1', 'DNS_2', 'subnet'], 'workstation_profile'),
            'codes'   => $this -> flagService -> getFilters(['options'], 'options_list'),
            'status'  => 'OK'
        ];
    }
}
