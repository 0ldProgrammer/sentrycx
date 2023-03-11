<?php


namespace App\Modules\Flags\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Container\Container;
use App\Modules\Flags\Services\FlagService;

class FlagListController extends Controller {
    /** @var FlagService  $flagService Instance of FlagService */
    private $flagService;
    /**
     * Constructor dependencies
     *
     * @param Container $continaer
     * @return void
     **/
    function __construct(Container $container){
        $this -> flagService = $container -> get('FlagService');
    }

    /**
     * Handles the API Request
     *
     * @param Request $request
     * @param Response $response
     * @return void
     **/
    function __invoke(Request $request){
        $page = $request -> query('page');

        $per_page = $request -> query('per_page', 20);

        $conditions = $request -> only(
            'account','agent_name','category_name', 'ISP', 
            'VLAN', 'DNS_1', 'DNS_2', 'subnet', 'location', 
            'status_info', 'category', 'code', 'connection', 'old_unresolved');

        $sort = $request -> only('sortBy', 'sortOrder');

        if( $sort )
            $this -> flagService -> setSort( $sort['sortBy'], $sort['sortOrder']);

        $user = $this -> getUser( $request );

        return $this -> flagService -> query($user, $page, $per_page, $conditions );
    }
}
