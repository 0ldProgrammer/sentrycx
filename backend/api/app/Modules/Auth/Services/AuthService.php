<?php

namespace App\Modules\Auth\Services;

use \Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use \GuzzleHttp\Client;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AuthService {

    private $api;
    private $endpoint;
    /**
     *
     * Dependencies
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct( Client $client ) {
        $this -> api = $client;
        $this -> endpoint = getenv('AUTH_API_URL');
    }

    /**
     * Retrieve the user details
     *
     *
     * @param String $email Email Address
     * @return type
     * @throws conditon
     **/
    public function getUser( $email = '') {
        $user = DB::table('users') -> where('email', $email ) -> first();

        if( !$user )
            throw new ModelNotFoundException("User not found by $email");
        
        return $user;
    }

    /**
     *  TODO : Move this to auth service
     * Retrieve the current token user from the auth api
     *
     * @return Response
     * @author
     **/
    public function authorizeRequest( $access_token ){
        $endpoint = $this -> endpoint . '/api/current-user';

        $response = $this -> api -> request('GET', $endpoint, [
            'headers' => [ 'Authorization' =>  "Bearer $access_token"]
        ]);

        return json_decode( $response -> getBody() );
    }

    /**
     * TODO : Move this to auth service
     * Generate a token that can be used for accessing other
     * api endpoint within this gateway
     *
     * @return void
     * @author
     **/
    public function generateToken( $user ){
        $payload = [
            "user" => $user,
            "role"  => 'SUPERUSER', # Harcoded for now
            "exp" => time() + 28800, #8 hours
            "iat" => time()
        ];

        $authSecret = getenv('AUTH_API_SECRET');

        $access_token = JWT::encode($payload, $authSecret );

        return $access_token;
    }

}
