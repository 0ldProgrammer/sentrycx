<?php

namespace App\Modules\Maintenance\Controllers;

use Illuminate\Contracts\Container\Container;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserUpdateController extends Controller {

    /** @var \App\Modules\Maintenance\Services\UserService $service  */
    protected $service;

    /**
     *
     * Constructor Dependencies
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct( Container $container ){
        $this -> service = $container -> get('UserService');
    }

    /**
     *
     * Handles the updating of user
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __invoke( Request $request , $id = 0 ){
        $args = $request -> only('username','email', 'firstname','location', 'scope_access', 'account_access');

        $this -> service -> save( $args, $id  );

        return ['status' => 'OK', 'message' => 'User has been updated! Advise user to re-login'];
    }
}
