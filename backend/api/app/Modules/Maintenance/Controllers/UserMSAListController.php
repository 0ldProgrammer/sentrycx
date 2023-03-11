<?php 

namespace App\Modules\Maintenance\Controllers;

use App\Modules\Maintenance\Services\UserService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Container\Container;

class UserMSAListController extends Controller {

    /** @var UserService $service */
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
        $this -> service = $container -> get('UserService');
    }

    /**
     *
     * Handles the list of MSA
     *
     * @param Request $request
     * @return type
     * @throws conditon
     **/
    public function __invoke(Request $request){
        return $this -> service -> fetchMSA();
        return ['status' => 'OK', 'data' => $data];
    }
}