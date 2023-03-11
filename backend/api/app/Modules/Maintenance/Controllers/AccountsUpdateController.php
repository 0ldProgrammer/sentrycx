<?php 

namespace App\Modules\Maintenance\Controllers;

use App\Modules\WorkstationModule\Services\AccountsService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Container\Container;

class AccountsUpdateController extends Controller {

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
    public function __invoke(Request $request, $id = 0){
        $data = $request -> only('name', 'is_active', 'need_device_check', 'need_hostfile_url', 'has_securecx', 'check_sites_devices');

        $this -> service -> save( $data, $id );

        return ['status' => 'OK', 'data' => $data];
    }
}