<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \Firebase\JWT\JWT;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\DB;
use \Firebase\JWT\ExpiredException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Modules\Maintenance\Services\UserConfigService;

class ValidateController extends Controller {
    private $endpoint = '';

    /** @var \App\Modules\Auth\Services\AuthService $authService*/
    private $authService;

    /** @var UserConfigService $configService  */
    protected $configService;
    /**
     *
     * Constructor dependencies
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct(Container $container ){
        $this -> api = new \GuzzleHttp\Client();
        $this -> endpoint = getenv('AUTH_API_URL');
        $this -> authService = $container -> get('AuthService');
        $this -> configService = $container -> get('UserConfigService');
    }

    /**
     * Validate the access token to retrieve
     * the logged in SSO attributes
     *
     * @return void
     * @author
     **/

    public function __invoke(Request $request){
        $token = $request -> bearerToken();

        $authSecret = getenv('AUTH_API_SECRET');

        // TODO : Refractor this, trasnsfer to service
        try {
            $authorization = JWT::decode($token, $authSecret, ['HS256'] );

            $response  = $this -> authService -> authorizeRequest( $authorization -> access_token );

            $employee_info = $response -> data;

            $user = $this -> authService -> getUser( $employee_info -> mail );

            $token = $this -> authService -> generateToken( $user );

            $this -> configService -> setUser( $user -> id );

            return response() -> json([
                'token' => $token,
                'response' => $employee_info,
                'user'     => $user,
                'config'   => $this -> configService -> all()
            ],200);
        }
        catch( ModelNotFoundException $e ){
            return response() -> json(['status' => 'ERROR', 'msg' => $e -> getMessage() ], 404);
        }
        catch( GuzzleException $e ){
            return response() -> json(['status' => 'ERROR', 'msg' => 'Token Expired. Try again.' ], 401);
        }
        catch( ExpiredException $e ){
            return response() -> json(['status' => 'ERROR', 'msg' => 'Token Expired. Try again.' ], 401);
        }
        
    }

}
