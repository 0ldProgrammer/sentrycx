<?php

namespace App\Modules\Zoho\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Container\Container;
use App\Http\Controllers\Controller;


class AccessTokenController extends Controller {

    /** @var \App\Modules\Zoho\Services\ZohoService $service description */
    protected $service;

    /**
     * Constructor Dependencies
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct( Container $container ){
        $this -> service = $container -> get('ZohoService');
    }

    /**
     *
     * Handles the generation of OAUTH URL
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __invoke(Request $request ){
        $code = $request -> input('code');

        $access_token = $this -> service -> accessToken( $code );

        if( !$access_token )
            return response()->json(['status' => 'ERROR', 'msg' => 'Access Denied'], 400);

        return [
            'status' => 'OK',
            'access_token'   => $access_token
        ];
    }
}
