<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Container\Container;
use \Firebase\JWT\JWT;

class AuthenticateController extends Controller {
    private $endpoint = '';
    /**
     *
     * Constructor dependencies
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct()
    {
        $this -> endpoint = getenv('AUTH_API_URL');

    }

    /**
     * Validate the access token to retrieve
     * the logged in SSO attributes
     *
     * @return void
     * @author
     **/
    public function __invoke(Request $request){
        $client_token = getenv('AUTH_API_TOKEN');

        return [
            "url" => $this -> endpoint . "/employee/oauth?client={$client_token}",
            "status" => "OK"
        ];
    }

}
