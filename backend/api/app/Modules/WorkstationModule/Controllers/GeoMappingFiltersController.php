<?php

namespace App\Modules\WorkstationModule\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Container\Container;
use Illuminate\Support\Arr;
use App\Modules\WorkstationModule\Services\WorkstationService;

class GeoMappingFiltersController extends Controller {

    /** @var WorkstationService  $workstationService Instance of workstation service */
    protected $workstationService = null;

    /**
     * Constructor Dependencies
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct(Container $container)
    {
        $this -> workstationService = $container -> get('WorkstationService');
    }

    /**
     * Handles the filters of geo mapping
     *
     * @param Request $request
     * @return type
     * @throws conditon
     **/
    public function __invoke(Request $request ){
        $filters = $this -> workstationService -> getGeoMappingFilters();

        return $filters;
    }
}
