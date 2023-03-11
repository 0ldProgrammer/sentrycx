<?php

namespace App\Modules\Zoho\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Container\Container;
use App\Http\Controllers\Controller;


class OAuthController extends Controller {

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
        $this -> service -> setState( $request -> query('state') );

        return [
            'status' => 'OK',
            'URL'    => $this -> service -> oauth()
        ];
    }
}
