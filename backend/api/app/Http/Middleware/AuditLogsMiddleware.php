<?php

namespace App\Http\Middleware;

use Firebase\JWT\JWT;
use Closure;
use App\Modules\HistoricalRecords\Services\AuditRecordsService;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuditLogsMiddleware
{
    protected $service;
    // private $logService;


    const AUDIT_LOG_REF = [
        'lock-screen' => 'PC Lock',
        'wipeout' => 'Wipeout',
        'status' => 'Status',
        'batch-status' => 'Batch Status',
        'init' => 'Event Logs',
        'hostfile' => 'Host file',
        'mtr-request' => 'MTR Request',
        'send-request' => 'Send Request',
        'zoho' => 'Zoho'
    ];

   /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(AuditRecordsService $auditRecordsService)
    {
        $this -> service = $auditRecordsService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $agent_name = null;
        $agent_workstation_name = null;
        $red_flag_agent_name = null;
        $account_name = null;
        
        $name = $request->route()[1]['as'];

        $event_name = self::AUDIT_LOG_REF[$name]; 

        $worker_id = $request -> route('worker_id');

        switch($event_name) {
            case 'Status':
                $event_name = $request->input('status');
                $redflagData = $this->getRedflagDashboardData(['rd.id' => $request->route('id')]);
                $worker_id = $redflagData[0]->worker_id;
                $agent_name = $redflagData[0]->agent_name;
                $agent_workstation_name = $redflagData[0]->station_number;
                break;
            case 'Batch Status':
                $event_name = "BATCH {$request->input('status')}";
                $args = $request -> input('conditions');
                $agent_name = isset($args['account']) ? $args['account'] : '';
                break;
            case 'MTR Request':
                $worker_id = $request->input('worker_id');
                break;
            case 'Send Request':
                $worker_id = $request->input('worker_id');
                $redflagData = $this->getRedflagDashboardData(['rd.id' => $request->route('id')]);
                $agent_name = $redflagData[0]->agent_name;
                $agent_workstation_name = $redflagData[0]->station_number;
                break;
        }
  
        $user = $this->getUser( $request );
        
        if ($worker_id) {
            $agent_data = $this->getWorkstation(['wp.worker_id' => $worker_id]);
            $agent_name = $agent_data[0]->agent_name;
            $agent_workstation_name = $agent_data[0]->station_name;
        }
        
        $data = [
            'event' => $event_name, 
            'user' => $user->username,
            'affected_agent' => $agent_name,
            'workstation_number' => $agent_workstation_name
        ];

        if (!$worker_id) {
            $request -> input('worker_id');
        }

        if ($worker_id) {
            $this->service->setWorkerID($worker_id);
        }
      
        $this->service->log($data); 
        return $next($request);
    }

    private function getUser( Request $request ){
        $auth_secret = getenv('AUTH_API_SECRET');

        $token = $request->bearerToken();

        $decoded =  JWT::decode( $token, $auth_secret ,['HS256']);

        return $decoded -> user;
    }

        /**
     *
     * Retrieve the workstation profile
     *
     * @param Array $conditions
     * @param Int $type , 1 for MTR_REQUEST, 2 for WORKSTATION_PROFILE
     * @return type
     * @throws conditon
     **/
    private function getWorkstation($conditions = []){
        $query = DB::table('workstation_profile as wp');
        $query -> leftJoin('agent_connections AS ac', 'ac.worker_id', '=', 'wp.worker_id' );

        foreach( $conditions as $field => $value )
            $query -> where( $field, $value );

        $query -> where('redflag_id', 0);
        $query -> orderBy('wp.id' , 'asc');
        $query -> limit(1);

        return $query -> get();
    }

    private function getRedflagDashboardData($conditions = []){
        $query = DB::table('redflag_dashboard as rd');
        $query -> leftJoin('workstation_profile AS wp', 'wp.worker_id', '=', 'rd.worker_id' );

        foreach( $conditions as $field => $value )
            $query -> where( $field, $value );

        $query -> orderBy('rd.id' , 'asc');
        $query -> limit(1);

        return $query -> get();
    }
}
