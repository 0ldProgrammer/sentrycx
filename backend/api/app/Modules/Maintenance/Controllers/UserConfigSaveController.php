<?php 

namespace App\Modules\Maintenance\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Container\Container;
use App\Http\Controllers\Controller;
use App\Modules\Maintenance\Services\UserConfigService;

class UserConfigSaveController extends Controller {

    /** @var UserConfigService $service*/
    protected $service;

    /**
     *
     * Constructor 
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct(Container $container){
        $this -> service = $container -> get('UserConfigService');
    }

    /**
     *
     * Handles the saving of user config
     *
     * @param Request $request
     * @param Integer $userID
     * @return Response
     * @throws conditon
     **/
    public function __invoke(Request $request, $id ){
        $this -> service -> setUser( $id );

        $data = $request -> only('config_name', 'value' );
        
        $this -> service -> set($data['config_name'], $data['value']);

        

        return ['status' => 'OK', 'data' => $data['value'] ];
    }
}