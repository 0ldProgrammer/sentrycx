<?php

namespace App\Modules\Maintenance\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Arr;

class MSAListController extends Controller {

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
     * Handles the list of msa from automated_users table
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __invoke(Request $request ){
        $page = $request -> query('page', 1);
        $per_page = $request -> query('per_page', 20 );
        return $this -> service -> msaQuery($page, $per_page, $conditions = null);
    }
}
