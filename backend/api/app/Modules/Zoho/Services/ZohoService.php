<?php

namespace App\Modules\Zoho\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;

class ZohoService {

    /** @var String $endpoint description */
    protected $endpoint = "https://assist.zoho.com/api/v2/";

    /** @var String $endpoint description */
    protected $auth = "https://accounts.zoho.com/oauth/v2/";

    /** @var String $clientID description */
    protected $state = 'temp';

    /**
     *
     * Getter for State
     *
     **/
    public function setState( $state ){
        $this -> state = $state;
    }

    /** @var String $redirectURI description */
    protected $redirectURI = '';

    /** @var String $grant_type description */
    protected $grantType = 'authorization_code';

    /** @var String $scope description */
    protected $scope = 'ZohoAssist.sessionapi.CREATE';

    /** @var String $responseType description */
    protected $responseType = 'code';

    /** @var String $type description */
    protected $accesType = 'online';

    /** @var String $clientID description */
    protected $clientID;

    /** @var String $secretKey description */
    protected $secretKey;

    /** @var String $refresh_token description */
    protected $refreshToken;


    /**
     *
     * Constructor dependencies
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct( $clientID , $secretKey, $redirectURI, $refresh_token ){
        $this -> clientID  = $clientID;
        $this -> secretKey = $secretKey;
        $this -> redirectURI = $redirectURI;
        $this -> refreshToken = $refresh_token;
    }

    /**
     *
     * Generate Oauth URL
     *
     * @return String
     * @throws conditon
     **/
    public function oauth(){
        $query_params = http_build_query([
            'scope' => $this -> scope,
            'state' => $this -> state,
            'client_id'     => $this -> clientID,
            'response_type' => $this -> responseType,
            'redirect_uri'  => $this -> redirectURI,
            'access_type'   => $this -> accesType
        ]);

        return "{$this->auth}auth?$query_params";
    }

    /**
     * Generate Access Token
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function accessToken( $code ){
        // $client = new Client();
        // $form_params = [
        //     'code' => $code,
        //     'client_id'     => $this -> clientID,
        //     'client_secret' => $this -> secretKey,
        //     'redirect_uri'  => $this -> redirectURI,
        //     'grant_type'    => $this -> grantType
        // ];

        // $response = $client->post("https://accounts.zoho.com/oauth/v2/token", ['form_params' => $form_params]);

        // $authentication = json_decode( $response -> getBody() -> getContents() );

        // if( !property_exists($authentication, 'access_token'))
        //     return false;

        // return $authentication->access_token ;
        $params = ['code' => $code ];

        return $this -> _generateAcceessToken( $params );
    }

    /**
     *
     * Generate Access Token from refresh token
     *
     * @param String $refresh_token Description
     * @return type
     * @throws conditon
     **/
    public function refreshAccessToken(  ){
        $this -> grantType = 'refresh_token';

        $params = ['refresh_token' => $this -> refreshToken ];

        return $this -> _generateAcceessToken($params);
    }

    /**
     *
     * Generate Access Token
     *
     * @param Array $extraParams Description
     * @return type
     * @throws conditon
     **/
    public function _generateAcceessToken( $extra_params ){
        $client = new Client();

        $form_params = [
            'client_id'     => $this -> clientID,
            'client_secret' => $this -> secretKey,
            'redirect_uri'  => $this -> redirectURI,
            'grant_type'    => $this -> grantType
        ];

        $form_params = array_merge( $form_params, $extra_params );

        $response = $client->post("https://accounts.zoho.com/oauth/v2/token", ['form_params' => $form_params]);

        $authentication = json_decode( $response -> getBody() -> getContents() );

        if( !property_exists($authentication, 'access_token'))
            return false;

        return $authentication->access_token ;
    }


    /**
     *
     * Create Remote Control session
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function createRemoteSession( $access_token = '')
    {
        $client = new Client(['headers' => [
            'Authorization' => "Zoho-oauthtoken $access_token"
        ]]);

        $response = $client->post("{$this->endpoint}session?", [] );

        $data = json_decode( $response -> getBody() -> getContents() );

        return $data;
    }

    /**
     *
     * Extract Report
     *
     * @param Integer $starttime unixtimestamp in milliseconds
     * @param Integer $endtime unixtimestamp in milliseconds
     * @return type
     * @throws conditon
     **/
    public function extractReport( $starttime, $endtime ){
        $client = new Client();

        $access_token = $this -> refreshAccessToken();

        $response = $client -> get('https://assist.zoho.com/api/v2/reports',[
            'query' => [
                'type' => 'all',
                'fromdate' => $starttime,
                'todate'   => $endtime,
                'index'    => 1,
                'count'    => 50
            ],
            'headers' => [
                'Content-Type'  => 'application/x-www-form-urlencoded;charset=UTF-8',
                'Authorization' => "Zoho-oauthtoken $access_token"
            ]
        ]);

        $data = json_decode( $response -> getBody() -> getContents() );

        return $data;
    }
}
