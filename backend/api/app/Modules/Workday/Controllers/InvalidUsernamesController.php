<?php 

namespace App\Modules\Workday\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Modules\Workday\Services\EmployeeService;

class InvalidUsernamesController extends Controller {

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
     * Handles the fetching of invalid usernames list
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/

    public function __invoke(Request $request ){

        $page = $request -> query('page', 1);

        $per_page = $request -> query('perPage');
        
        $conditions = Arr::only($request -> query(), ['user', 'event', 'affected_agent', 'workstation_number', 'worker_id', 'startDate', 'endDate' ] );
       
        $sort = $request -> only('sortBy', 'sortOrder');

        if( $sort )
            $this -> service -> setSort( $sort['sortBy'], $sort['sortOrder']);
        
        return $this -> service -> getInvalidUsernames($page, $conditions, $search = '', $per_page);
    }
}