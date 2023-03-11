<?php

namespace App\Modules\Maintenance\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Container\Container;
use App\Http\Controllers\Controller;

class UserAddController extends Controller {

    /** @var \App\Modules\Maintenance\Services\UserService $service  */
    protected $service;

    /**
     *
     * Constructor Dependencies
     *
     * @param Containr $container
     * @return type
     * @throws conditon
     **/
    public function __construct(Container $container ){
        $this -> service = $container -> get('UserService');
    }

    /**
     *
     * Handles adding of user
     *
     * @param Request $request
     * @return type
     * @throws conditon
     **/
    public function __invoke(Request $request ){
        $args = $request -> only('username', 'email', 'firstname', 'location','account_access', 'scope_access');

        $this -> service -> save( $args );

        return ['status' => 'OK', 'message' => 'User successfully created!'];
    }
}
