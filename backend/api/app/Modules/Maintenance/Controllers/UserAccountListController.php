<?php

namespace App\Modules\Maintenance\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request;

class UserAccountListController extends Controller {

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
        $this -> maintenanceService = $container -> get('MaintenanceService');
    }

    /**
     *
     * Handles the listing of account lists
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __invoke(Request $request ){
        $accountList = $this -> maintenanceService -> getUserAccountList();

        return json_encode(array('accountList' => $accountList));
    }
}
