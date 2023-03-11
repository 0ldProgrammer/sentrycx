<?php

namespace App\Modules\Maintenance\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Arr;

class UserListController extends Controller {

    /** @var \App\Modules\Maintenance\Services\UserService $service description */
    protected $service;

    /**
     *
     * Consctructor dependencies
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct(Container $container ){
        $this -> service = $container -> get('UserService');
    }

    /**
     *
     * Handles the list of users
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __invoke(Request $request ){

        $conditions = Arr::only($request -> query(), ['location','account_access'] );
        $page = $request -> query('page', 1);
        $per_page = $request -> query('per_page', 20 );
        return $this -> service -> query($page, $per_page, $conditions);
    }
}
