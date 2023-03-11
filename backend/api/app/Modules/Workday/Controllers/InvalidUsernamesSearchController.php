<?php 

namespace App\Modules\Workday\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Modules\Workday\Services\EmployeeService;

class InvalidUsernamesSearchController extends Controller {

    /** @var EmployeeService $service */
    protected $service;

    /**
     *
     * Constructor Method
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct(Container $container){
        $this -> service = $container -> get('EmployeeService');
    }

    /**
     *
     * Handles the fetching of invalid usernames
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/

    public function __invoke(Request $request ){

        $search = $request -> query('search');
        
        $page = $request -> query('page', 1);
        
        $conditions = Arr::only($request -> query(), ['user', 'event', 'affected_agent', 'workstation_number' ] );

        $sort = $request -> only('sortBy', 'sortOrder');

        if( $sort )
            $this -> service -> setSort( $sort['sortBy'], $sort['sortOrder']);
        
        return $this -> service -> getInvalidUsernames($page, $conditions, $search);
    }
}