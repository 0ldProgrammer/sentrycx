<?php

namespace App\Modules\Zoho\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Container\Container;
use App\Http\Controllers\Controller;
use App\Modules\Flags\Events\DesktopNotificationBroadcast;

class SessionController extends Controller {

    /** @var \App\Modules\Zoho\Services\ZohoService $service description */
    protected $service;

    /** @var \App\Modules\WorkstationModule\Services\WorkstationService $service description */
    protected $workstationService;

    /** @var \App\Modules\Zoho\Services\ZohoLogService $service description */
    protected $zohoLogService;

    /**
     * Constructor Dependencies
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct( Container $container ){
        $this -> service = $container -> get('ZohoService');

        $this -> workstationService = $container -> get('WorkstationService');

        $this -> zohoLogService     = $container -> get('ZohoLogService');
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
        $access_token  = $request -> input('access_token');

        $worker_id     = $request -> input('state');

        $agent_session = $this -> workstationService -> getAgentSession( $worker_id );

        $rdp_session = $this -> service -> createRemoteSession( $access_token );

        $this -> _dispatch( $agent_session, $rdp_session -> representation -> customer_url );

        $this -> zohoLogService -> initReport($worker_id, $rdp_session -> representation -> session_id );

        return [
            'status'  => 'OK',
            'URL'    =>  $rdp_session -> representation -> technician_url
        ];
    }

    /**
     *
     * Dispatch the Desktop notification
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function _dispatch( $session_id, $url ){
        event( new DesktopNotificationBroadcast ([
            'title'   => "RDP Request",
            'message' => " A NOC Engineer would like to access your PC. Click here",
            'session_id' => $session_id,
            'url' => $url
        ]));
    }


}
