<?php

namespace App\Modules\Maintenance\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Container\Container;
use App\Http\Controllers\Controller;

class UserDeleteMSAController extends Controller {

    /** @var \App\Modules\Maintenance\Services\UserService $service description */
    protected $service = null;

    /**
     *
     * Constructor dependencies
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
     * Handles the removing of MSA
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __invoke(Request $request, $id ){
        $this -> service -> deleteMSA( $id );

        return ['status' => 'OK', 'message' => 'Programme MSA successfully deleted!'];
    }
}