<?php
namespace App\Modules\Maintenance\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Container\Container;
use App\Http\Controllers\Controller;


class UserDetailsController extends Controller {

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
    public function __invoke(Request $request, $id = 0 ){
        $user = $this -> service -> get( $id );
        $user -> location = explode(",", $user->location);
        $user -> account_access = explode(",", $user->account_access);

        return [
            'status' => 'OK',
            'data'   => $user
         ];
    }
}

