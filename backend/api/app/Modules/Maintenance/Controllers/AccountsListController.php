<?php 

namespace App\Modules\Maintenance\Controllers;

use App\Modules\WorkstationModule\Services\AccountsService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Container\Container;

class AccountsListController extends Controller {

    /** @var AccountsService $service */
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
        $this -> service = $container -> get('AccountsService');
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
        $per_page = $request -> query('per_page', 20);

        $page = ($request -> query('page')=='undefined'?1:$request -> query('page'));
        $conditions = $request -> query('search');
        $is_active = $request -> query('isActive');
        $mediaCheck = $request -> query('mediaCheck');
        $has_securecx = $request -> query('secureCX');
        
        return $this -> service -> query( $page, $conditions, $is_active, $mediaCheck, $per_page, $has_securecx );
    }
}