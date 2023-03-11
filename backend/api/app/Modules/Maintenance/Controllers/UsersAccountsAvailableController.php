<?php

namespace App\Modules\Maintenance\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request;

class UsersAccountsAvailableController extends Controller {

    /** @var \App\Modules\Maintenance\Services\OrganizationService $service description */
    protected $service;

    /**
     *
     * Constructor Dependencies
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct(Container $container ){
        $this -> service = $container -> get('OrganizationService');
    }

    /**
     *
     * Handles the listing of sites lists
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __invoke(Request $request ){
        return [
            'status' => 'OK',
            'data'   => $this -> service -> getAccounts()
       ];
    }
}
