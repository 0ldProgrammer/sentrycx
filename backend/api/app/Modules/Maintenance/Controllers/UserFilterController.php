<?php

namespace App\Modules\Maintenance\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Container\Container;

class UserFilterController extends Controller {
    /** @var \App\Modules\Maintenance\Services\UserService $userService */
    private $userService;

    /**
     * Constructor dependencies
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct( Container $container ) {
        $this -> userService = $container -> get('UserService');
    }


    /**
     * Handles the route function
     *
     * @param Request $request
     * @return type
     * @throws conditon
     **/
    public function __invoke(Request $request ){

        $fields = $request -> query('fields', false);

        return [
            'data'    => $this -> userService -> getFilters( $fields ),
            'status'  => 'OK'
        ];
    }
}
