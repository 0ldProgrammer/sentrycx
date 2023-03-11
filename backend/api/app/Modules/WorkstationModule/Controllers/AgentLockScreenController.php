<?php

namespace App\Modules\WorkstationModule\Controllers;

use Illuminate\Container\Container;
use App\Http\Controllers\Controller;
use App\Modules\WorkstationModule\Events\AdminLapsRequestBroadcast;
use App\Modules\WorkstationModule\Events\AgentWipeoutBroadcast;
use App\Modules\WorkstationModule\Events\AgentLockBroadcast;
use Illuminate\Http\Request;
use App\Modules\WorkstationModule\Services\PotentialTriggerService;
use App\Modules\WorkstationModule\Services\AgentConnectionService;
use App\Modules\Workday\Services\EmployeeService;
use \Firebase\JWT\JWT;

class AgentLockScreenController extends Controller {
  
    /** @var PotentialTriggerService $service */
    /** @var AgentConnectionService $service */
    /** @var EmployeeService $service */
    protected $service;
    protected $agentConnection;
    protected $employee;

    /*
     *
     * Constructor dependency method
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct(Container $container ){
        $this -> service = $container -> get('PotentialTriggerService');
        $this -> agentConnection = $container -> get('AgentConnectionService');
        $this -> employee = $container -> get('EmployeeService');
    }
    /**
     *
     * Handles the retrieval of agent connection details by id
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/

    public function __invoke($worker_id = 0, Request $request){
        $requested_by = $request -> input('requested_by');
        $event = $request -> input('event');
        $agent_name = $request -> input('agent_name');
        $dateTime = date('Y-m-d H:i:s');
        $password = '@dm1n@local.net';

        $agentDetails       = $this -> agentConnection -> getConnectionByEmployee($worker_id);
        $employeeDetails    = $this -> employee -> getEmployeeDetails($worker_id);

        $potentialTriggerData = array(
            'event'                 => $event,
            'triggered_by'          => $requested_by,
            'datetime_triggered'    => $dateTime,
            'agent_name'            => $agent_name,
            'worker_id'             => $worker_id,
            'email'                 => $employeeDetails->email,
            'position'              => $employeeDetails->job_profile,
            'site'                  => $employeeDetails->location_name,
            'manager'               => $employeeDetails->supervisor_full_name,
            'execution_date'        => $dateTime,
            'execution_type'        => $request -> input('execution_type'),
            'remarks'               => $request -> input('remarks')

        );

        
        
        // if($this -> service -> insert($potentialTriggerData))
            switch($event):
                
                case 'PC Lock':
                    // if($agentDetails->is_active)
                        // event( new AgentLockBroadcast($agentDetails->session_id, $password));
                        
                        $this -> dispatchBroadcast($worker_id, $dateTime, 'user_reset');
                        

                        // return ['status' => 'OK', 'msg' => 'Request has been Sent', $json_decode->password];
                break;
                case 'PC Wipeout':
                    // if($agentDetails->is_active)
                        $this -> dispatchBroadcast($worker_id, $dateTime, 'admin_reset');
                        // event( new AgentWipeoutBroadcast($agentDetails->session_id,  $password));
                        //return ['status' => 'OK', 'msg' => 'Request has been Sent', $authorization];
                break;

            endswitch;   

        //if($agentDetails->is_active)
            // event( new AgentLockBroadcast($agentDetails->session_id, 'ToBeDiscussed' ));

        // $authSecret = getenv('AUTH_API_SECRET');
        // $access_token = JWT::encode($params, $authSecret );
        
        // if($this -> service -> insert($requested_by, $event, $agent_name, $access_token, $worker_id)):
            
        //     return ['status' => 'OK', 'msg' => 'Request has been Sent','params' => $request -> input('params')];
            
        // endif; 


    }

    public function dispatchBroadcast( $worker_id, $execution_date, $event ) {
        $connection = $this -> agentConnection ->  getConnectionByEmployee( $worker_id );
        $params = [ 'execution_date' => $execution_date, 'event' => $event ];
        $args = [
            'callback_url' => getenv('APP_URL') . "/workstation/{$worker_id}/wipeout",
            'station_name' => $connection -> station_name,
            'params' => $params
        ];

        echo '<pre>';
        print_r($args);

        event( new AdminLapsRequestBroadcast( $args ) );
        echo "DONE DISPATCHING TO ADMIN LAPS APP";
    }

}
