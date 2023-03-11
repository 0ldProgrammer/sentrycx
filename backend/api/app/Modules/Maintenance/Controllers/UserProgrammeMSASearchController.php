<?php 

namespace App\Modules\Maintenance\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Modules\Maintenance\Services\UserService;

class UserProgrammeMSASearchController extends Controller {

    /** @var UserService $service */
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
        $this -> service = $container -> get('UserService');
    }

    /**
     *
     * Handles searching of MSA
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/

    public function __invoke(Request $request ){

        $search = $request -> query('search');

        $page = $request -> query('page', 1);

        $sort = $request -> only('sortBy', 'sortOrder');

        if( $sort ) {
            $this -> service -> setSort( $sort['sortBy'], $sort['sortOrder']);
        }

        return ['status' => 'OK', 'data' => $this -> service -> getProgrammeMSA($page, $conditions = null, $search) ];

    }

}