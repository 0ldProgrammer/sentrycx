<?php 

namespace App\Modules\Maintenance\Controllers;

use App\Modules\WorkstationModule\Services\AccountsService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Container\Container;

class AccountsDeleteController extends Controller {

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
    public function __invoke(Request $request, $id = 0 ){
        $this -> service -> delete($id);

        return ['status' => 'OK'];
    }
}