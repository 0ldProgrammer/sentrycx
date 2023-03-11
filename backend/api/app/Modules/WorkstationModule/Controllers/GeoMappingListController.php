<?php
namespace App\Modules\WorkstationModule\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Container\Container;
use Laravel\Lumen\Routing\Controller as BaseController;
use App\Modules\WorkstationModule\Services\WorkstationService;
use Illuminate\Support\Arr;

class GeoMappingListController extends BaseController {

    /** @var App\Modules\Maintenance\Services\WorkstationService  $maintenanceService description */
    protected $workstationService;
    /**
     * Constructor Method
     * Define constructor dependencies here
     *
     * @return void
     **/
    public function __construct(Container $container ){
        $this -> workstationService = $container -> get ('WorkstationService');
    }
    

    /**
     * Handles the Code list
     *
     * @param Request $request
     * @return Response
     * @throws conditon
     **/
    public function __invoke( Request $request)
    {
        $page = $request -> query('page');
        $per_page = $request -> query('perPage',20);
        
        $search = $request -> query('search');
        $conditions = Arr::only($request -> query(), ['city','account', 'country', 'location'] );

        $sort = $request -> only('sortBy', 'sortOrder');

        if( $sort )
            $this -> workstationService -> setSort( $sort['sortBy'], $sort['sortOrder']);


        return $this -> workstationService -> getMappingList($page, $per_page,$search, $conditions);
        
    }


}
