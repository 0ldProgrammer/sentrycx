<?php 

namespace App\Modules\Maintenance\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Container\Container;
use App\Http\Controllers\Controller;
use App\Modules\Maintenance\Services\UserConfigService;

class UserConfigController extends Controller {
    /** @var UserConfigService $service */
    protected $service;

    /**
     *
     * Dependency injections 
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct(Container $container ){
        $this -> service = $container -> get('UserConfigService');
    }

    /**
     *
     * Handles the fetching of user config
     *
     * @param Type $var Description
     * @return Response
     * @throws conditon
     **/
    public function __invoke(Request $request, $id ){
        $this -> service -> setUser( $id );

        $config_name = $request -> query('config_name');

        return [ 'data' => $this -> service -> get( $config_name ) ];
    }
}