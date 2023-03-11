<?php

namespace App\Modules\WorkstationModule\Services;

use App\Mail\AgentApplicationsMail;
use App\Modules\WorkstationModule\Models\AgentConnection;
use App\Modules\WorkstationModule\Models\AgentApplications;
use App\Modules\WorkstationModule\Models\ApplicationsList;
use Illuminate\Support\Facades\DB;
use App\Modules\WorkstationModule\Helpers\ConnectedAgentsQueryHelper;
use App\Modules\WorkstationModule\Models\OnlineStats;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border;
use App\Modules\WorkstationModule\Traits\RequestParserTrait;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class AgentConnectionService {
    use RequestParserTrait;

    /** @var AgentConnectino $agentConnection description */
    protected $agentConnection;

    /** @var OnlineStats $onlineStats description */
    protected $onlineStats;

    const HEADERS_REF = [
        'agent_name' => 'AgentName',
        'host_name' => 'Username',
        'position' => 'Position',
        'connection' => 'Connection',
        'connection_type' => 'Connection',
        'agent' => 'Agent',
        'net_type' => 'Agent',
        'location' => 'Location',
        'account' => 'Account',
        'ISP' => 'ISP',
        'download_speed' => 'DOWN Speed',
        'upload_speed' => 'UP Speed',
        'mtr_highest_avg' => 'HighestAVG',
        'mtr_highest_loss' => 'HighestLOSS',
        'host_ip_address' => 'IP',
        'vpn' => 'VPN',
        'VLAN' => 'VLAN',
        'DNS_1' => 'DNS1',
        'DNS_2' => 'DNS2 ',
        'subnet' => 'Subnet',
        'average_latency' => 'AVGLAT',
        'packet_loss' => 'APLOSS',
        'jitter' => 'Jitter',
        'mos' => 'MOS',
        'updated_at' => 'Timestamp',
        'audio' => 'Audio',
        'mic' => 'Mic',
        'video' => 'Video',
        'mos' => 'MOS',
        'ram' => 'Ram',
        'ram_usage' => 'Ram Usage',
        'disk' => 'Disk',
        'free_disk' => 'Free Disk',
        'cpu' => 'CPU',
        'cpu_util' => 'CPU UTIL',
        'Throughput_percentage' => 'Throughput',
        'lob' => 'LOB',
        'programme_msa' => 'Programme MSA',
        'supervisor_email_id' => 'Supervisor Email',
        'supervisor_full_name' => 'Supervisor',
        'country' => 'Country',
        'agent_email' => 'Agent Email',
        'worker_id' => 'Worker ID',
        'station_number' => 'Workstation',
        'timestamp' => 'Timestamp',
        'status' => 'Status',
        'position' => 'Position',
        'job_profile' => 'Job Profile',
        'manager' => 'Manager',
        'securecx_gate_1' => 'SecureCX Gate1',
        'securecx_gate_2' => 'SecureCX Gate2',
        'securecx_gate_3' => 'SecureCX Gate3',
        'manager_email' => 'Manager Email'
    ];

    const DATA_REF = [
        'agent_name' => 'ac.agent_name',
        'host_name' => 'wp.host_name',
        'connection' => 'ac.connection_type',
        'connection_type' => 'ac.connection_type',
        'agent' => 'wp.net_type as agent',
        'net_type' => 'wp.net_type',
        'location' => 'ac.location',
        'account' => 'ac.account',
        'ISP' => 'wp.ISP',
        'download_speed' => 'wp.download_speed',
        'upload_speed' => 'wp.upload_speed',
        'mtr_highest_avg' => 'ac.mtr_highest_avg',
        'mtr_highest_loss' => 'ac.mtr_highest_loss',
        'host_ip_address' => 'wp.host_ip_address',
        'vpn' => "CASE WHEN wp.net_type like '%VPN%' THEN 'Yes' ELSE 'No' END as vpn",
        'VLAN' => 'wp.VLAN',
        'DNS_1' => 'wp.DNS_1',
        'DNS_2' => 'wp.DNS_2 ',
        'subnet' => 'wp.subnet',
        'average_latency' => 'wp.average_latency',
        'packet_loss' => 'wp.packet_loss',
        'jitter' => 'wp.jitter',
        'mos' => 'wp.mos',
        'updated_at' => 'ac.updated_at',
        'audio' => 'amd.audio',
        'mic' => 'amd.mic',
        'video' => 'amd.video',
        'ram' => 'wp.ram',
        'ram_usage' => 'wp.ram_usage',
        'disk' => 'wp.disk',
        'free_disk' => 'wp.free_disk',
        'cpu' => 'wp.cpu',
        'cpu_util' => 'wp.cpu_util',
        'Throughput_percentage' => 'wp.throughput_percentage',
        'lob' => 'ac.lob',
        'programme_msa' => 'ac.programme_msa',
        'supervisor_email_id' => 'ac.supervisor_email_id',
        'supervisor_full_name' => 'ac.supervisor_full_name',
        'country' => 'ac.country',
        'agent_email' => 'ac.agent_email',
        'worker_id' => 'ac.worker_id',
        'station_number' => 'wp.station_number',
        'timestamp' => 'ac.updated_at',
        'status' => 'aux_list.name',
        'position' => 'ac.job_profile as position',
        'job_profile' => 'ac.job_profile',
        'manager' => 'ac.supervisor_full_name as manager',
        'securecx_gate_1' => 'asu.securecx_gate_1',
        'securecx_gate_2' => 'asu.securecx_gate_2',
        'securecx_gate_3' => 'asu.securecx_gate_3',
        'manager_email' => 'ac.supervisor_email_id'
    ];

    const SELECT_REF = [
        'account' => 'ac.account',
        'ISP' => 'wp.ISP',
        'lob' => 'ac.lob',
        'programme_msa' => 'ac.programme_msa',
        'position' => 'ac.job_profile',
        'connection' => 'ac.connection_type',
        'agent' => 'wp.net_type',
        'vlan' => 'wp.VLAN',
        'dns_1' => 'wp.DNS_1',
        'dns_2' => 'wp.DNS_2',
        'subnet' => 'wp.subnet',
        'mos' => 'wp.mos',
        'supervisor' => 'ac.supervisor_full_name',
        'connection_type' => 'ac.connection_type',
        'net_type' => 'wp.net_type',
        'position' => 'ac.job_profile'
    ];
    
    /** @var $user */
    protected $user = null;

    public function setUser($user){
        $this->user = $user;
    }

    protected $inactive = false;

    public function setInactive( $inactive = true ){
        $this->inactive = $inactive;
    }

    /**
     *
     * Undocumented function long description
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct(AgentConnection $agentConnection, OnlineStats $onlineStats){
        $this->agentConnection = $agentConnection;
        $this->onlineStats = $onlineStats;
    }

     /** @var Array $var description */
     const FIELDS_MAPPING = [
        'account'       => 'rd.account',
        'category_name' => 'cat.name',
        'ISP'           => 'wp.ISP',
        'VLAN'          => 'wp.VLAN',
        'DNS_1'         => 'wp.DNS_1',
        'DNS_2'         => 'wp.DNS_2',
        'subnet'        => 'wp.subnet',
        'location'      => 'rd.location',
        'status_info'   => 'rd.status_info',
        'category'      => 'cat.name',
        'code'          => 'ol.options',
        'average_latency' => 'wp.average_latency', 
        'packet_loss'   => 'wp.packet_loss', 
        'jitter'        => 'wp.jitter', 
        'mos'           => 'wp.mos',
        'aux_status'    => 'ac.aux_status',
        'net_type'      => 'wp.net_type'
    ];


    /** @var String $sortBy */
    protected $sortBy = null;
    protected $sortOrder = 'asc';

    public function setSort($field, $order){
        $this->sortBy = $field;
        $this->sortOrder =  strtolower( $order );
    }

    /**
     * 
     * Update the agent_connections
     *
     * @return type
     * @throws conditon
     **/
    public function updateAuxStatus($worker_id, $status = 'ONLINE' ) {
       return AgentConnection::where('worker_id', $worker_id)
           ->update(['aux_status' => $status ]);
    }


    /**
     *
     * Generate query builder needed for MOS View
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    private function _connectionStatsQuery($search = "", $group = true ){
        $query = DB::table('agent_connections as ac');

        if( $group )
            $query->select('account');

        $query->addSelect(DB::raw("COUNT(account) as connected" ) );
        $query->addSelect(DB::raw("AVG( mos ) as avg_mos" ) );
        $query->addSelect(DB::raw("SUM( CASE WHEN ac.connection_type = 'WIRED' THEN 1 ELSE 0 END) as wired" ) );
        $query->addSelect(DB::raw("SUM( CASE WHEN ac.connection_type = 'WIRELESS' THEN 1 ELSE 0 END) as wireless" ) );
        $query->addSelect(DB::raw("SUM( CASE WHEN wp.net_type = 'WAH' THEN 1 ELSE 0 END) as wah" ) );
        $query->addSelect(DB::raw("SUM( CASE WHEN wp.net_type = 'B&M' THEN 1 ELSE 0 END) as bm" ) );
        $query->addSelect(DB::raw("SUM( CASE WHEN wp.net_type like '%VPN%' THEN 1 ELSE 0 END) as vpn" ) );
        // $query->addSelect(DB::raw("SUM( CASE WHEN wp.host_ip_address LIKE '192.%' THEN 1 ELSE 0 END) as wah" ) );
        // $query->addSelect(DB::raw("SUM( CASE WHEN wp.host_ip_address LIKE '10.%' THEN 1 ELSE 0 END) as bm" ) );

        $query->leftJoin('workstation_profile AS wp', function( $join ){
            $join->on( 'wp.worker_id', '=', 'ac.worker_id')
            ->on('wp.id', '=', DB::raw("(SELECT max(id) from workstation_profile WHERE workstation_profile.worker_id = ac.worker_id AND redflag_id=0)"));
        });

        $query->where('is_active', TRUE);
        $query->where('is_admin', FALSE);
        $query->where('redflag_id', 0);
        if($search != "")
            $query ->where('account', 'like', "%$search%");

        if(!empty( $this->user->location )){
            $location_list   =  explode(",", $this-> user->location );
            $query->whereIn('ac.location', $location_list);
        }

        return $query;
    }
    /**
     *
     * Query MOS Overview Stats
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function getConnectionStats($search = "", $page = 1, $per_page = 20 ){
        $query = $this->_connectionStatsQuery( $search );

        $query->groupBy('account');

        return $query->paginate($per_page, ['*'], 'page', $page );
    }

    /**
     *
     * Query MOS Overview Stats
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function getConnectionStatsBreakdown($group_field = null, $search = ''){
        $query = $this->_connectionStatsQuery( $search );

        $query->groupBy('account');

        $query->addSelect( $group_field );
        $query->groupBy( $group_field );

        return $query->get()->groupBy( 'account' );
    }

    /**
     *
     * Query the base breakdown  COuntry and location breakdown
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function getBaseBreakdown ($group_fields = [], $search = ''){
        $query = $this->_connectionStatsQuery( $search );

        $query->addSelect('account','country', 'location');

        $query->groupBy('account','country', 'location');

        return $query->get();
    }

    /**
     *
     * Generate the Total count 
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function getTotalStats($search = ''){
        $query = $this->_connectionStatsQuery($search, false);
        return $query->first();
    }


    
    /**
     * TODO : Transfer this to ReportService
     * Query the overall connected device stats
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function getConnectionStatsOverall($grouped = 'ac.location'){
        DB::insert("SET sql_mode = ''");

        $query = DB::table('agent_connections as ac');

        if( $grouped ){
            $query->groupBy('ac.location');
            $query->select('ac.location as site', 'ac.country');
        }

        $query->addselect(DB::raw("COUNT(account) as connected" ) );
        $query->addSelect(DB::raw("SUM( CASE WHEN ac.connection_type = 'WIRED' THEN 1 ELSE 0 END) as wired" ) );
        $query->addSelect(DB::raw("SUM( CASE WHEN ac.connection_type = 'WIRELESS' THEN 1 ELSE 0 END) as wireless" ) );
        $query->addSelect(DB::raw("SUM( CASE WHEN wp.net_type = 'WAH' THEN 1 ELSE 0 END) as wah" ) );
        $query->addSelect(DB::raw("SUM( CASE WHEN wp.net_type = 'B&M' THEN 1 ELSE 0 END) as bm" ) );
        $query->addSelect(DB::raw("SUM( CASE WHEN wp.net_type = 'VPN' THEN 1 ELSE 0 END) as vpn" ) );
        

        $query->leftJoin('workstation_profile AS wp', function( $join ){
            $join->on( 'wp.worker_id', '=', 'ac.worker_id')
            ->on('wp.id', '=', DB::raw("(SELECT max(id) from workstation_profile WHERE workstation_profile.worker_id = ac.worker_id AND redflag_id=0)"));
        });

        $query->where('is_active', TRUE);
        $query->where('is_admin', FALSE);
        $query->where('redflag_id', 0);

        return $query->get();
    }

    /**
     *
     * Saves the connectetion status
     *
     * @param Array $data
     * @return type
     * @throws conditon
     **/
    public function generateStats( $params ){
        $this->onlineStats = new OnlineStats();
        foreach( $params as $field => $value )
            $this->onlineStats->$field = $value;

        $this->onlineStats->save();
    }

    /**
     *
     * Logs the current logged in agents into log for reporting purposes
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function logOnlineAgents(){
        $sql_string = "
            INSERT INTO agent_connections_log(created_at, updated_at, agent_name,agent_email,worker_id,station_name,location,account,country,connection_type,net_type,job_profile,lob,msa_client,programme_msa,is_admin)
            SELECT 
                CURRENT_TIMESTAMP as created_at, 
                CURRENT_TIMESTAMP as updated_at, 
                ac.agent_name,
                ac.agent_email,
                ac.worker_id,
                ac.station_name,
                ac.location,
                ac.account,
                ac.country,
                ac.connection_type,
                wp.net_type,
                ac.job_profile,
                ac.lob,
                ac.msa_client,
                ac.programme_msa,
                ac.is_admin
            FROM agent_connections ac
            LEFT JOIN (
            SELECT DISTINCT worker_id, net_type FROM workstation_profile WHERE redflag_id=0
            ) wp ON wp.worker_id = ac.worker_id 
            WHERE ac.is_active=TRUE
        ";

        DB::select( DB::raw( $sql_string ));
    }

    /**
     * 
     * Update the agent_connections
     *
     * @return type
     * @throws conditon
     **/
    public function updateConnection($data, $done_processing = false) {

		$entry = (object) $data;

        $connection = AgentConnection::where('worker_id', $entry->worker_id)->firstOrNew();

        // TODO : Convert this into dynamic approach
        $connection->agent_name       = $entry->firstname." ".$entry->lastname;
        $connection->agent_email      = $entry->email;
        $connection->worker_id        = $entry->worker_id;
        $connection->station_name     = $entry->station_number;
        $connection->location         = $entry->location;
        $connection->account          = $entry->account;
        $connection->country          = $entry->country;
        $connection->mtr_host         = $entry->mtr_host;
        $connection->mtr_highest_avg  = $entry->mtr_highest_avg;
        $connection->mtr_highest_loss = $entry->mtr_highest_loss;
        $connection->mtr_result       = $entry->MTR;
        $connection->session_id       = $entry->session_id;
        $connection->is_active        = TRUE;
        $connection->is_admin         = ( $entry->is_admin  == 'Admin' ) ? true : false;
        $connection->aux_status       = $entry->aux_status;
        $connection->connection_type  = $entry->connection_type;

        $connection->job_profile 	= $this->extractFromData( $data, 'job_profile');
        $connection->lob 			= $this->extractFromData( $data, 'lob');
        $connection->msa_client 	= $this->extractFromData( $data, 'msa_client'); 
        $connection->programme_msa 	= $this->extractFromData( $data, 'programme_msa'); 
        $connection->supervisor_email_id = $this->extractFromData( $data, 'supervisor_email_id'); 
        $connection->supervisor_full_name = $this->extractFromData( $data, 'supervisor_full_name'); 

        if( $done_processing ){
            $connection->processing = FALSE;
		}

        if( ! empty($entry->session_id)){
            Redis::hmset(
				'DeviceStatus.'.$entry->session_id, 
				[
					'id' => $entry->session_id, 
					'worker_id'=> $entry->worker_id, 
					'dateTime' => time(), 
					'count' => 1
				]
			);
        }
		
        $connection->save();
    }


    /**
     *
     * Checks if there are updates to agent connection
     * This will be used to identify caching counter
     * NOTE : Monitor the server_time and ph_time, currently ph_time is being used
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function countUpdatedConnections($server_time, $ph_time ){
        return AgentConnection::where('updated_at', '>', $ph_time ) 
           ->orWhere('last_logged_in', '>', $ph_time)
           ->count();
    }


    /**
     * Get Employee Info on Agent Connection
     * NOTE : This will be faster rather than checking directly from cnx_employees
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function getConnectionByEmployee($worker_id = 0 ){
        return  DB::table('agent_connections')->where('worker_id', $worker_id )->first();
    }

    /**
     * Get employee session based on account
     * NOTE : This will be faster rather than checking directly from cnx_employees
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function getSessionByAccount($account, $is_active = true ){
        return  DB::table('agent_connections')
           ->addSelect(['session_id', 'is_active', 'worker_id'])
           ->where('account', $account ) 
           ->where('is_active', $is_active )
           ->where('is_admin', FALSE)
           ->get();
    }


    /**
     * Get Employee Session ID
     *
     * @param String $worker_id
     * @return type
     * @throws conditon
     **/
    public function getAgentSession( $worker_id = 0 ){
        $connection = DB::table('agent_connections')
           ->where('worker_id', $worker_id ) 
           ->first();

        return $connection->session_id;
    }

    /**
     * Count the agentConnections which can be used to compare with cachecd data
     * 
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function countConnections($conditions = []){
        $query = $this->_queryInit( $conditions );
        
        return $query->count();
    }

    /**
     * 
     *
     * Retrieve all the inactive agents that were not able to login 
     * based on X Days
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function getInactiveConnections($page = 1, $conditions = [], $per_page = 20 ){
        $query = $this->_queryInit( $conditions );

        return $query->paginate( $per_page, ['*'], 'page', $page);
    }

     /**
     *
     * Retrieve all the agent connections 
     * 
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function getConnections($page = 1 , $conditions = [], $per_page = 20, $search = '') {
        $query = $this->_queryInit( $conditions, $search );
        
        return $query->paginate($per_page, ['*'], 'page', $search ? 1 : $page );

    }

    public function getSecurecx($page = 1 , $conditions = [], $per_page = 20, $search = '') {
        
        $query = AgentConnection::from('agent_connections as ac');

        $yesterday = Carbon::yesterday()->format('Y-m-d');

        $query = ConnectedAgentsQueryHelper::condition( $query , $conditions );

        $query->select([
            'ac.agent_name', 'ac.account', 'ac.location', 'wp.station_number','wp.host_name',
            'wp.net_type', 'wp.station_number', 'ac.connection_type', 'streaming_primary_host_name', 
            'streaming_primary_telnet_result', 'streaming_secondary_host_name', 'streaming_secondary_telnet_result', 
            'status_primary_host_name', 'status_primary_telnet_result', 'status_secondary_host_name', 'status_secondary_telnet_result'
        ]);
        
        $query->leftJoin('workstation_profile AS wp', function( $join ){
            $join->on( 'wp.worker_id', '=', 'ac.worker_id')
            ->on('wp.id', '=', DB::raw("(SELECT max(id) from workstation_profile WHERE workstation_profile.worker_id = ac.worker_id AND redflag_id=0)"));
        });
        
        $query->leftJoin('agent_media_device as amd', 'amd.worker_id', '=', 'ac.worker_id' );
        $query->leftJoin('securecx_urls_streaming as sus', 'sus.worker_id', '=', 'ac.worker_id' );
        $query->leftJoin('accounts', 'accounts.name', '=', 'ac.account');
        $query->where('is_admin', FALSE);
        $query->where('redflag_id', 0);
        $query->where('accounts.has_securecx', true);

        if  ($search != "") {
            $query -> where( function( $que ) use ($search){
                $que -> where('ac.account', 'like', "%$search%");
                $que -> orWhere('ac.agent_name', 'like', "%$search%");
                $que -> orWhere('ac.location', 'like', "%$search%");
                $que -> orWhere('wp.station_number', 'like', "%$search%");
            });
        }

        if( $this->sortBy) {
            $query->orderBy( $this->sortBy, $this->sortOrder );
        } else {
            $query->orderBy('ac.id', 'desc');
        }

        return $query->paginate($per_page, ['*'], 'page', $search ? 1 : $page );
        
    }

    /**
     *
     * Create query builder which can then be used for paginate or counting data
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function _queryInit($conditions = [], $search = ''){
        $query = AgentConnection::from('agent_connections as ac');

        $yesterday = Carbon::yesterday()->format('Y-m-d');

        $query = ConnectedAgentsQueryHelper::condition( $query , $conditions );

        $query->select([
            'ac.*', 'wp.host_ip_address', 'ac.job_profile as position', 'ac.updated_at as updated_at_aging','ac.created_at','wp.desktop_app_version',
            'wp.station_number','wp.subnet','wp.DNS_1','wp.DNS_2', 'wp.gateway',
            'wp.VLAN','wp.ISP', 'wp.download_speed','wp.upload_speed', 'wp.host_name',
            'wp.average_latency', 'wp.packet_loss', 'wp.jitter', 'wp.mos', 'wp.isp_fullname',
            'amd.audio', 'amd.mic', 'amd.video', 'asu.securecx_gate_1', 'asu.securecx_gate_2', 'asu.securecx_gate_3',
            'ac.worker_id AS employee_number','ac.agent_email AS email','ac.supervisor_email_id',
            'ac.supervisor_full_name','ac.country','ac.job_profile', 'ac.supervisor_full_name as manager',
            'ac.location AS location_name','ac.msa_client','ac.programme_msa','wp.net_type', 'wp.Throughput_percentage', 'wp.station_number'
        ]);

        $query->addSelect(DB::raw("CASE WHEN wp.net_type like '%VPN%' THEN 'Yes' ELSE 'No' END as vpn") );
        
        $query->leftJoin('workstation_profile AS wp', function( $join ){
            $join->on( 'wp.worker_id', '=', 'ac.worker_id')
            ->on('wp.id', '=', DB::raw("(SELECT max(id) from workstation_profile WHERE workstation_profile.worker_id = ac.worker_id AND redflag_id=0)"));
        });

        // $query->leftJoin('cnx_employees as wd', 'wd.employee_number', '=', 'ac.worker_id' );
        
        $query->leftJoin('agent_media_device as amd', 'amd.worker_id', '=', 'ac.worker_id' );
        $query->leftJoin('agent_securecx_urls as asu', 'asu.worker_id', '=', 'ac.worker_id' );
        
        $query->where('is_admin', FALSE);

        $query->where('redflag_id', 0);

        if  ($search != "") {
            $query -> where( function( $que ) use ($search){
                $que -> where('ac.account', 'like', "%$search%");
                $que -> orWhere('ac.agent_name', 'like', "%$search%");
                $que -> orWhere('ac.location', 'like', "%$search%");
                $que -> orWhere('wp.station_number', 'like', "%$search%");
            });
        }

        // TODO : Convert this into private function 
        //        for additional conditions
        if( $this->sortBy && $this->sortBy !== 'updated_at_aging') {
            $query->orderBy( $this->sortBy, $this->sortOrder );
        } else if ($this->sortBy && $this->sortBy === 'updated_at_aging') {
            $query->orderBy( $this->sortBy, $this->sortOrder === 'asc' ? 'desc' : 'asc' );
        } else {
            $query->orderBy('ac.id', 'desc');
        }
            
        if( $this->inactive ){
            $inactive_days = date("Y-m-d H:i:s", strtotime( getenv('AGENT_INACTIVE_DAYS') . ' days'));
            $query->where('ac.updated_at', '<', $inactive_days );
        }
        
        return $query;
    }

    public function getWorkstationsDashboard($page = 1 , $conditions = [], $per_page = 20, $search = '')
    {
        $query = DB::table('agent_connections as ac');

        $query = ConnectedAgentsQueryHelper::condition( $query , $conditions );

        $query->select([
            'ac.*', 'wp.host_ip_address', 'wp.date_created', 'wp.net_type as agent',
            'wp.station_number','wp.ram', 'wp.ram_usage', 
            'wp.disk', 'wp.free_disk', 'wp.cpu', 'wp.cpu_util',
            'wp.desktop_app_version', 'amd.audio', 'amd.mic', 'amd.video', 
            'ac.worker_id AS employee_number','ac.agent_email AS email','ac.supervisor_email_id',
            'ac.supervisor_full_name','ac.country','ac.job_profile',
            'ac.location AS location_name','ac.msa_client','ac.programme_msa',
            'wp.mos','wp.throughput_percentage','wp.net_type'
        ]);
        
        $query->leftJoin('workstation_profile AS wp', function( $join ){
            $join->on( 'wp.worker_id', '=', 'ac.worker_id')
            ->on('wp.id', '=', DB::raw("(SELECT max(id) from workstation_profile WHERE workstation_profile.worker_id = ac.worker_id AND redflag_id=0)"));
        });

        // $query->leftJoin('cnx_employees as wd', 'wd.employee_number',  '=', 'ac.worker_id' );

        $query->leftJoin('agent_media_device as amd', 'amd.worker_id', '=', 'ac.worker_id' );
        
        $query->where('is_admin', FALSE);

        $query->where('ac.is_active', TRUE);

        $query->where('redflag_id', 0);

        if  ($search != "") {
            $query->where('ac.account', 'like', "%$search%");
            $query->orWhere('ac.agent_name', 'like', "%$search%");
            $query->orWhere('ac.location', 'like', "%$search%");
            $query->orWhere('station_number', 'like', "%$search%");
        }

        if( $this->sortBy )
            $query->orderBy( $this->sortBy, $this->sortOrder );
        else
            $query->orderBy('ac.id', 'desc');
        
        return $query->paginate($per_page, ['*'], 'page', $page );
        
    }




    /**
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function getConnectedToc($page = 1 , $conditions = [], $per_page = 20)
    {
        $query = DB::table('agent_connections as ac');

        $query = ConnectedAgentsQueryHelper::condition( $query , $conditions );
        
        $query->addSelect('*');
        $query->addSelect('ac.updated_at as agent_updated_at');
        $query->addSelect('aux_list.name as aux_name');

        $query->where('is_admin', TRUE);

        $query->leftJoin('workstation_profile AS wp', function( $join ){
            $join->on( 'wp.worker_id', '=', 'ac.worker_id')
            ->on('wp.id', '=', DB::raw("(SELECT max(id) from workstation_profile WHERE workstation_profile.worker_id = ac.worker_id AND redflag_id=0)"));
        });

        $query->leftJoin('aux_list', 'aux_list.aux_status', '=', 'ac.aux_status');

        $query->where('redflag_id', 0);

        return $query->paginate($per_page, ['*'], 'page', $page );
    }

  /**
     *
     * Tag the agent connection as processing
     * This identifies the service that user already send a request to the agent
     *
     * @param String $session_id
     * @param Boolean $start  - True to tag as start, False if already done with the process
     * @return Boolean
     * @throws conditon
     **/
    public function process($session_id = '', $start = TRUE, $mtr_host = '' ){
        $update_data = ['processing' => $start ];
        if( $mtr_host )
            $update_data['mtr_host'] = $mtr_host;
        return DB::table('agent_connections')
           ->where('session_id', $session_id)
           ->update( $update_data );
    }

    /**
     *
     * Updates the status whether the connection is active or inactive
     *
     * @param String $session_id
     * @param Boolean $active
     * @return Boolean
     * @throws conditon
     **/
    public function updateStatus($session_id = '', $active = TRUE){
        $update_data = ['is_active' => $active ];
        if( !$active ) {
            $timestamp = date("Y-m-d H:i:s");
            $update_data['last_logged_in'] = $timestamp;
        }

        return DB::table('agent_connections')
           ->where('session_id', $session_id )
           ->update( $update_data );
    }

    /**
     *
     * Updates the threshold status of the conencted agent
     * This will identify if the agent has a problem in one of their application
     *
     * @param String $worker_id
     * @param Boolean $status
     * @return type
     * @throws conditon
     **/
    public function updateThresholdStatus( $worker_id, $status = True){
        return DB::table('agent_connections')
           ->where('worker_id', $worker_id)
           ->update(['has_threshold' => $status]);
    }

    /**
     *
     * Tag the agent as disable making the app lite mode
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function disableAgent( $worker_id, $disable  = true ){
        return DB::table('agent_connections')
           ->where('worker_id', $worker_id)
           ->update(['is_disabled' => $disable]);
    }

    /**
     *
     * Retrieve the connected agent data by id
     *
     * @param String $session_id
     * @param Int $type 1 for MTR_REQUEST, 2 for WORKSTATION_PROFILE_REQUEST
     * @return type
     * @throws conditon
     **/
    public function getAgent($connection_id = '', $type = 1){
        return DB::table('agent_connections AS ac')
           ->leftJoin('progress_results AS pr','pr.worker_id', '=', 'ac.worker_id' )
           ->addSelect('ac.*')
           ->addSelect('pr.progress')
           ->where('ac.id', $connection_id)
            //->where('pr.type', $type)
           ->first();
    }

    /**
     *
     * Retrieve the agent connection that have empty 
     *
     * @return type
     * @throws conditon
     **/
    public function updateAgentProfile(){
        $outdated = DB::table('agent_connections AS ac')
           ->leftJoin('cnx_employees AS ce', 'ce.employee_number', '=', 'ac.worker_id') 
           ->select('ac.worker_id', 'ce.job_profile','ce.lob', 'ce.msa_client', 'ce.programme_msa', 'ce.supervisor_email_id', 'ce.supervisor_full_name')
           ->whereNull('ac.supervisor_email_id')
           ->get();

        DB::beginTransaction();
        foreach( $outdated as $employee ) {
            echo " UPDATING {$employee->worker_id} " . PHP_EOL;
            $employee_array = (array) $employee;
            DB::table('agent_connections')->where('worker_id', $employee->worker_id )-> update( $employee_array );
        }

        DB::commit();
    }

    /**
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function getExcelPdfReport($report_type, $conditions = [], $headers = '', $timezone = '')
    {
        $include_agent_name = array('agent_name');
        $new_data_array = array();
        $list_of_headers = array();
        $list_of_data_selected = array();
        $fetch_exist = array();
        $all_headers = [
            'agent_name', 'host_name', 'connection','agent', 'station_number', 'position', 'location', 'account', 
            'manager', 'mtr_highest_avg', 'mtr_highest_loss', 'host_ip_address',
            'vpn', 'VLAN', 'DNS_1', 'DNS_2', 'subnet', 
            'ISP', 'download_speed', 'upload_speed', 'average_latency', 'packet_loss', 'jitter', 
            'mos', 'updated_at', 'updated_at_aging', 'Throughput_percentage', 'lob', 'programme_msa',
            'supervisor_email_id', 'securecx_gate_1', 'securecx_gate_2', 'securecx_gate_3', 'media_device'
        ];

        $headers_to_array = explode(',', $headers);
        foreach($headers_to_array as $head) {
            array_push($include_agent_name, $head);
        }
    
        foreach($all_headers as $val) {
            if (in_array($val, $include_agent_name)) {
                if ($val == 'media_device') {
                    array_push($fetch_exist, 'audio', 'mic', 'video');
                } else if ($val == 'supervisor_email_id') {
                    array_push($fetch_exist, 'manager_email');
                } else if ($val == 'updated_at_aging') {
                    continue;
                } else {
                    array_push($fetch_exist, $val);
                }
            }
            
        }

        foreach($fetch_exist as $header) {
            $header_name = self::HEADERS_REF[$header]; 
            $data_name = self::DATA_REF[$header]; 
            array_push($list_of_headers, $header_name);
            array_push($list_of_data_selected, $data_name);
        }
        
        $data_selected = implode(',',$list_of_data_selected);

        $query = DB::table('agent_connections as ac');

        $query = ConnectedAgentsQueryHelper::condition( $query , $conditions );
        $query->selectRaw($data_selected);

        $query->leftJoin('workstation_profile AS wp', function( $join ){
            $join->on( 'wp.worker_id', '=', 'ac.worker_id')
            ->on('wp.id', '=', DB::raw("(SELECT max(id) from workstation_profile WHERE workstation_profile.worker_id = ac.worker_id AND redflag_id=0)"));
        });

        $query->leftJoin('cnx_employees as wd', 'wd.employee_number', '=', 'ac.worker_id' );
        
        $query->leftJoin('agent_media_device as amd', 'amd.worker_id', '=', 'ac.worker_id' );
        $query->leftJoin('agent_securecx_urls as asu', 'asu.worker_id', '=', 'ac.worker_id' );
        
        $query->where('is_admin', FALSE);

        $query->where('redflag_id', 0);

        if( $this->sortBy && $this->sortBy !== 'updated_at_aging') {
            $query->orderBy( $this->sortBy, $this->sortOrder );
        } else if ($this->sortBy && $this->sortBy == 'updated_at_aging') {
            $query->orderBy( 'updated_at', $this->sortOrder === 'asc' ? 'desc' : 'asc' );
        } else {
            $query->orderBy('ac.id', 'desc');
        }

        $data = $query->get();

        $data_array = json_decode(json_encode($data), true);

        foreach($data_array as $value){
            if(isset($value['updated_at'])){
                $get_current_date = Carbon::createFromTimestamp(strtotime($value['updated_at']))
                    ->timezone($timezone)
                    ->toDateTimeString();
    
                $value['updated_at'] = Carbon::parse($get_current_date)->format('Y-m-d h:i:s a');
            }
            
            if(isset($value['mtr_highest_avg'])){
                if($value['mtr_highest_avg']){
                    $result = str_replace(',', '', $value['mtr_highest_avg']);
                    $value['mtr_highest_avg'] = $value['mtr_highest_avg'] !== ' - ' ? number_format($result, 2, '.', ',').' ' : '';
                } else {
                    $value['mtr_highest_avg'] = $this->checkOnlyZeroes($value['mtr_highest_avg']) ? '0.00 ' : '';
                }
            }

            if(isset($value['mtr_highest_loss'])){
                if($value['mtr_highest_loss']){
                    $value['mtr_highest_loss'] = $value['mtr_highest_loss'] !== ' - ' ? sprintf("%.2f", $value['mtr_highest_loss']).' ' : '';
                } else {
                    $value['mtr_highest_loss'] = $this->CheckZeroes($value['mtr_highest_loss']) ? '0.00 ' : '';
                }     
            }

            if(isset($value['average_latency'])){
                if($value['average_latency']){
                    $value['average_latency'] = $value['average_latency'] !== ' - ' ? sprintf("%.2f", $value['average_latency']).' ' : '';
                } else {
                    $value['average_latency'] = $this->CheckZeroes($value['average_latency']) ? '0.00 ' : '';
                }     
            }

            if(isset($value['packet_loss'])){
                if($value['packet_loss']){
                    $value['packet_loss'] = $value['packet_loss'] !== ' - ' ? sprintf("%.2f", $value['packet_loss']).'% ' : '';
                } else {
                    $value['packet_loss'] = $this->CheckZeroes($value['packet_loss']) ? '0.00% ' : '';
                }     
            }

            if(isset($value['download_speed'])) {
                $value['download_speed'] = $value['download_speed'] && $value['download_speed'] !== ' - ' ? $value['download_speed'].' Mbps' : '-';
            }

            if(isset($value['upload_speed'])) {
                $value['upload_speed'] = $value['upload_speed'] && $value['upload_speed'] !== ' - ' ? $value['upload_speed'].' Mbps' : '-';
            }

            array_push($new_data_array, $value);
        }
        
        $this->excelPdfStructure($report_type, $new_data_array, $list_of_headers);

    }

    private function CheckZeroes($input)
    {
        return preg_match('/^[0]*$/', $input);
    }


    public function generateExcelPdfReportFromDesktopDashboard($report_type, $conditions = [], $headers = '', $timezone = ''){
        
        $list_of_headers = array();
        $new_data_array = array();
        $list_of_data_selected = array();
        $fetch_exist = array();

        $all_headers = [
            'agent_name', 'job_profile', 'connection_type', 'agent', 'station_number', 'location', 'account', 'mos', 'ram', 'ram_usage',
            'disk', 'free_disk', 'cpu', 'cpu_util', 'Throughput_percentage', 'lob',
            'programme_msa', 'supervisor_email_id', 'supervisor_full_name', 'updated_at', 'media_device'
        ];
        $headers_to_array = explode(',', $headers);
        
        foreach($all_headers as $val) {
            if (in_array($val, $headers_to_array)) {
                if ($val == 'media_device') {
                    array_push($fetch_exist, 'audio', 'mic', 'video');
                } else {
                    array_push($fetch_exist, $val);
                }
            }
            
        }

        foreach($fetch_exist as $header) {
            $header_name = self::HEADERS_REF[$header]; 
            $data_name = self::DATA_REF[$header]; 
            array_push($list_of_headers, $header_name);
            array_push($list_of_data_selected, $data_name);
        }

        $data_selected = implode(',',$list_of_data_selected);
        
        $query = DB::table('agent_connections as ac');
        
        $query = ConnectedAgentsQueryHelper::condition( $query , $conditions );
        $query->selectRaw($data_selected);
        
        $query->leftJoin('workstation_profile AS wp', function( $join ){
            $join->on( 'wp.worker_id', '=', 'ac.worker_id')
            ->on('wp.id', '=', DB::raw("(SELECT max(id) from workstation_profile WHERE workstation_profile.worker_id = ac.worker_id AND redflag_id=0)"));
        });

        $query->leftJoin('cnx_employees as wd', 'wd.employee_number', '=', 'ac.worker_id' );

        $query->leftJoin('agent_media_device as amd', 'amd.worker_id', '=', 'ac.worker_id' );
        
        $query->where('is_admin', FALSE);

        $query->where('ac.is_active', TRUE);

        $query->where('redflag_id', 0);

        if( $this->sortBy ) {
            $query->orderBy( $this->sortBy, $this->sortOrder );
        } else {
            $query->orderBy('ac.id', 'desc');
        }

        $data = $query->get();
        
        $data_array = json_decode(json_encode($data), true);

        foreach($data_array as $value){
            if(isset($value['updated_at'])){
                $get_current_date = Carbon::createFromTimestamp(strtotime($value['updated_at']))
                    ->timezone($timezone)
                    ->toDateTimeString();
    
                $value['updated_at'] = Carbon::parse($get_current_date)->format('Y-m-d h:i:s a');
            }
            if (isset($value['ram']) && $value['ram'] !== '') {
                $value['ram'] = $value['ram'].'GB';
            }
            if (isset($value['ram_usage']) && $value['ram_usage'] !== '') {
                $value['ram_usage'] = $value['ram_usage'].'%';
            }
            if (isset($value['disk']) && $value['disk'] !== '') {
                $value['disk'] = $value['disk'].'GB';
            }
            if (isset($value['free_disk']) && $value['free_disk'] !== '') {
                $value['free_disk'] = $value['free_disk'].'%';
            }
            if (isset($value['cpu_util']) && $value['cpu_util'] !== '') {
                $value['cpu_util'] = $value['cpu_util'].'%';
            }    
            array_push($new_data_array, $value);
        }
        
        $this->excelPdfStructure($report_type, $new_data_array, $list_of_headers);
        
    }

    public function getSecurecxReport($report_type, $conditions = [], $timezone = '') {

        $new_data_array = array();

        $list_of_headers = [
            'Agent', 'Username', 'Connection', 'Net Type', 'Workstation', 'Location', 'Account', 
            'MOS', 'Avg Latency', 'Packet Loss', 'Jitter', 'Ram', 'Ram Usage', 'Disk', 'Free Disk', 
            'CPU', 'CPU Util', 'P1', 'P2', 'S1', 'S2'
        ];

        $query = AgentConnection::from('agent_connections as ac');

        $query = ConnectedAgentsQueryHelper::condition( $query , $conditions );

        $query->select([
            'ac.agent_name', 'wp.host_name', 'ac.connection_type', 'wp.net_type', 
            'wp.station_number', 'ac.location', 'ac.account', 'mos', 'average_latency', 'packet_loss', 
            'jitter', 'ram', 'ram_usage','disk', 'free_disk', 'cpu', 'cpu_util', 'streaming_primary_telnet_result', 
            'streaming_secondary_telnet_result', 'status_primary_telnet_result', 'status_secondary_telnet_result'
        ]);
        
        $query->leftJoin('workstation_profile AS wp', function( $join ){
            $join->on( 'wp.worker_id', '=', 'ac.worker_id')
            ->on('wp.id', '=', DB::raw("(SELECT max(id) from workstation_profile WHERE workstation_profile.worker_id = ac.worker_id AND redflag_id=0)"));
        });
        
        $query->leftJoin('agent_media_device as amd', 'amd.worker_id', '=', 'ac.worker_id' );
        $query->leftJoin('securecx_urls_streaming as sus', 'sus.worker_id', '=', 'ac.worker_id' );
        $query->leftJoin('accounts', 'accounts.name', '=', 'ac.account');
        $query->where('is_admin', FALSE);
        $query->where('redflag_id', 0);
        $query->where('accounts.has_securecx', true);

        if( $this->sortBy ) {
            $query->orderBy( $this->sortBy, $this->sortOrder );
        } else {
            $query->orderBy('ac.id', 'desc');
        }

        $data = $query->get();
        
        $data_array = json_decode(json_encode($data), true);

        foreach($data_array as $value){
            
            if (isset($value['ram']) && $value['ram'] !== '') {
                $value['ram'] = $value['ram'].'GB';
            }
            if (isset($value['ram_usage']) && $value['ram_usage'] !== '') {
                $value['ram_usage'] = $value['ram_usage'].'%';
            }
            if (isset($value['disk']) && $value['disk'] !== '') {
                $value['disk'] = $value['disk'].'GB';
            }
            if (isset($value['free_disk']) && $value['free_disk'] !== '') {
                $value['free_disk'] = $value['free_disk'].'%';
            }
            if (isset($value['cpu_util']) && $value['cpu_util'] !== '') {
                $value['cpu_util'] = $value['cpu_util'].'%';
            }    
            array_push($new_data_array, $value);
        }

        $this->excelPdfStructure($report_type, $new_data_array, $list_of_headers);
    }

    public function generateExcelPdfReportFromMOSView($report_type, $search, $breakdownField, $mosDataBreakdown) {
        
        $accountStats = json_decode(json_encode($mosDataBreakdown['account_stats']), true);
        $finalAccountStats = $accountStats['data'];
        $finalLocationStats = json_decode(json_encode($mosDataBreakdown['location_stats']), true);
        $finalCountryStats = json_decode(json_encode($mosDataBreakdown['country_stats']), true);
        $finalDetailedBreakdown = json_decode(json_encode($mosDataBreakdown['detailed_breakdown_stats']), true);
        $finalTotalStats = json_decode(json_encode($mosDataBreakdown['total_stats']), true);

        $list_of_headers = [
            'Account', 'Connected Devices', 'AVG MOS', 'Wired', 'Wireless',
            'WAH', 'BM', 'VPN', 'BYOD'
        ];

        $spreadsheet = new Spreadsheet();
        
        $sheet = $spreadsheet->getActiveSheet();

        $rowHeader = 6;
        $rowData = 7;
        $colLetters = 'C';
    
        $colHeader = $breakdownField === 'location' ? 3 : 4;
        
        foreach($list_of_headers as $value) {
            if($value == 'Account') {
                $sheet->getStyleByColumnAndRow(1, 6)->getFont()->setBold(true);
                $sheet->setCellValueByColumnAndRow(1, 6, $value);
                $sheet->getColumnDimension('A')->setAutoSize(true);
            } else {
                $sheet->getStyleByColumnAndRow($colHeader, $rowHeader)->getFont()->setBold(true);
                $sheet->setCellValueByColumnAndRow($colHeader, $rowHeader, $value);
                $sheet->getColumnDimension($colLetters)->setAutoSize(true);
                $colHeader++;
                $colLetters++;
            }
        }
        
        foreach($finalAccountStats as $accountStats) {
            $currentColData = 1;
            $colData = $breakdownField === 'location' ? 3 : 4;

            foreach($accountStats as $value) {
                if ($currentColData == 1) {
                    $sheet->setCellValueByColumnAndRow(1, $rowData, $value);
                } else {
                    $sheet->setCellValueByColumnAndRow($colData, $rowData, $value);
                    $colData++;    
                }
                $currentColData++;
            }
            $sheet->setCellValueByColumnAndRow($colData, $rowData, '0');
            $rowData++;

            if ($breakdownField === 'location') {
                foreach($finalLocationStats[$accountStats['account']] as $locationStats) {
                    $sheet->setCellValueByColumnAndRow(2, $rowData, $locationStats['location']);
                    $sheet->setCellValueByColumnAndRow(3, $rowData, $locationStats['connected']);
                    $sheet->setCellValueByColumnAndRow(4, $rowData, $locationStats['avg_mos']);
                    $sheet->setCellValueByColumnAndRow(5, $rowData, $locationStats['wired']);
                    $sheet->setCellValueByColumnAndRow(6, $rowData, $locationStats['wireless']);
                    $sheet->setCellValueByColumnAndRow(7, $rowData, $locationStats['wah']);
                    $sheet->setCellValueByColumnAndRow(8, $rowData, $locationStats['bm']);
                    $sheet->setCellValueByColumnAndRow(9, $rowData, $locationStats['vpn']);
                    $sheet->setCellValueByColumnAndRow(10, $rowData, '0');
                    $rowData++;
                }
            } else {
                foreach($finalCountryStats[$accountStats['account']] as $countryStats) {
                    $sheet->setCellValueByColumnAndRow(2, $rowData, $countryStats['country']);
                    $sheet->setCellValueByColumnAndRow(4, $rowData, $countryStats['connected']);
                    $sheet->setCellValueByColumnAndRow(5, $rowData, $countryStats['avg_mos']);
                    $sheet->setCellValueByColumnAndRow(6, $rowData, $countryStats['wired']);
                    $sheet->setCellValueByColumnAndRow(7, $rowData, $countryStats['wireless']);
                    $sheet->setCellValueByColumnAndRow(8, $rowData, $countryStats['wah']);
                    $sheet->setCellValueByColumnAndRow(9, $rowData, $countryStats['bm']);
                    $sheet->setCellValueByColumnAndRow(10, $rowData, $countryStats['vpn']);
                    $sheet->setCellValueByColumnAndRow(11, $rowData, '0');
                    $rowData++;

                    foreach($finalDetailedBreakdown[$accountStats['account']][$countryStats['country']] as $detailedBreakdown) {
                        $sheet->setCellValueByColumnAndRow(3, $rowData, $detailedBreakdown['location']);
                        $sheet->setCellValueByColumnAndRow(4, $rowData, $detailedBreakdown['connected']);
                        $sheet->setCellValueByColumnAndRow(5, $rowData, $detailedBreakdown['avg_mos']);
                        $sheet->setCellValueByColumnAndRow(6, $rowData, $detailedBreakdown['wired']);
                        $sheet->setCellValueByColumnAndRow(7, $rowData, $detailedBreakdown['wireless']);
                        $sheet->setCellValueByColumnAndRow(8, $rowData, $detailedBreakdown['wah']);
                        $sheet->setCellValueByColumnAndRow(9, $rowData, $detailedBreakdown['bm']);
                        $sheet->setCellValueByColumnAndRow(10, $rowData, $detailedBreakdown['vpn']);
                        $sheet->setCellValueByColumnAndRow(11, $rowData, '0');
                        $rowData++;
                    }
                }
            }
        }

        $colTotalData = $breakdownField === 'location' ? 3 : 4;

        $sheet->getStyleByColumnAndRow(1, $rowData)->getFont()->setBold(true);
        $sheet->setCellValueByColumnAndRow(1, $rowData, 'TOTAL');

        foreach($finalTotalStats as $totalStats) { 
            $sheet->setCellValueByColumnAndRow($colTotalData, $rowData, $totalStats); 
            $sheet->getStyleByColumnAndRow($colTotalData, $rowData)->getFont()->setBold(true);
            $colTotalData++;
        }
        $sheet->setCellValueByColumnAndRow($colTotalData, $rowData, '0');
        $sheet->getStyleByColumnAndRow($colTotalData, $rowData)->getFont()->setBold(true);

        $sheet->mergeCells("A1:A5");

        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('SentryCX');
        $drawing->setDescription('SentryCX');
        $drawing->setPath('angular2-logo-white.png'); 
        $drawing->setCoordinates('A1');
        $drawing->setWidthAndHeight(100, 100);
        $drawing->setWorksheet($sheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');
    
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }

    public function generateExcelPdfReportFromApplicationsView( $report_type, $applications_type, $applications_breakdown ) {
    
        $list_of_headers = array();
        $accountStats = json_decode(json_encode($applications_breakdown['accounts']), true);
        $userStats = json_decode(json_encode($applications_breakdown['users']), true);
        $final_location_stats = json_decode(json_encode($applications_breakdown['location']), true);
        $final_total_stats = json_decode(json_encode($applications_breakdown['total']), true);
        
        foreach ($accountStats[0] as $key => $value) {
            array_push($list_of_headers, $key);
        }

        $spreadsheet = new Spreadsheet();
        
        $sheet = $spreadsheet->getActiveSheet();
        $rowHeader = 6;
        $rowData = 7;
        $colLetters = 'C';
    
        $colHeader = 4;
        
        foreach($list_of_headers as $value) {
            if($value == 'account') {
                $sheet->getStyleByColumnAndRow(1, 6)->getFont()->setBold(true);
                $sheet->setCellValueByColumnAndRow(1, 6, ucfirst($value));
                $sheet->getColumnDimension('A')->setAutoSize(true);
            } else {
                $sheet->getStyleByColumnAndRow($colHeader, $rowHeader)->getFont()->setBold(true);
                $sheet->setCellValueByColumnAndRow($colHeader, $rowHeader, ucfirst($value));
                $sheet->getColumnDimension($colLetters)->setAutoSize(true);
                $colHeader++;
                $colLetters++;
            }
        }
        
        foreach($accountStats as $accountValues) {
 
            $sheet->setCellValueByColumnAndRow(1, $rowData, $accountValues['account']);
           
            $rowData++;
            
            foreach($final_location_stats[$accountValues['account']] as $locationStats) {

                $sheet->setCellValueByColumnAndRow(2, $rowData, $locationStats['location']);

                $rowData++;
            
                foreach($userStats[$accountValues['account']] as $user) {
                    $colDataUser = 3;
                    if($user['location'] == $locationStats['location']) {
                        foreach(array_slice($user, 2) as $user_value) {
                            $sheet->setCellValueByColumnAndRow($colDataUser, $rowData, $user_value);
                            $colDataUser++;
                        }
                        $rowData++;
                    }
                    
                }
                
            }       
        }

        $colTotalData = 4;

        $sheet->getStyleByColumnAndRow(1, $rowData)->getFont()->setBold(true);
        $sheet->setCellValueByColumnAndRow(1, $rowData, 'TOTAL');
        
        foreach($final_total_stats[0] as $totalStatsKey => $totalStats) { 
            foreach(array_slice($accountStats[0],1) as $accountStatskey => $value){
                if($accountStatskey == $totalStatsKey) {
                    $sheet->setCellValueByColumnAndRow($colTotalData, $rowData, $totalStats); 
                    $sheet->getStyleByColumnAndRow($colTotalData, $rowData)->getFont()->setBold(true);
                    $colTotalData++;
                }
            }
        }
        $sheet->getStyleByColumnAndRow($colTotalData, $rowData)->getFont()->setBold(true);

        $sheet->mergeCells("A1:A5");

        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('SentryCX');
        $drawing->setDescription('SentryCX');
        $drawing->setPath('angular2-logo-white.png'); 
        $drawing->setCoordinates('A1');
        $drawing->setWidthAndHeight(100, 100);
        $drawing->setWorksheet($sheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');
    
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');

    }

    public function generateExcelPdfReportFromConnectedTOC($report_type, $conditions = [], $headers = '', $timezone = ''){

        $list_of_headers = array();
        $list_of_data_selected = array();
        $fetch_exist = array();
        $new_data_array = array();

        $all_headers = [
            'agent_name', 'country', 'location', 'account', 'agent_email',
            'worker_id', 'host_ip_address', 'station_number', 'timestamp', 'status'
         
        ];
        $headers_to_array = explode(',', $headers);
        
        foreach($all_headers as $val) {
            if (in_array($val, $headers_to_array)) {
                array_push($fetch_exist, $val);
            }
        }

        foreach($fetch_exist as $header) {
            $header_name = self::HEADERS_REF[$header]; 
            $data_name = self::DATA_REF[$header]; 
            array_push($list_of_headers, $header_name);
            array_push($list_of_data_selected, $data_name);
        }

        $data_selected = implode(',',$list_of_data_selected);

        $query = DB::table('agent_connections as ac');

        $query = ConnectedAgentsQueryHelper::condition( $query , $conditions );
        $query->selectRaw($data_selected);

        $query->where('is_admin', TRUE);

        $query->leftJoin('workstation_profile AS wp', function( $join ){
            $join->on( 'wp.worker_id', '=', 'ac.worker_id')
            ->on('wp.id', '=', DB::raw("(SELECT max(id) from workstation_profile WHERE workstation_profile.worker_id = ac.worker_id AND redflag_id=0)"));
        });

        $query->leftJoin('aux_list', 'aux_list.aux_status', '=', 'ac.aux_status');

        $query->where('redflag_id', 0);

        $data = $query->get();

        $data_array = json_decode(json_encode($data), true);

        foreach($data_array as $status){
            if(isset($status['updated_at'])){
                $get_current_date = Carbon::createFromTimestamp(strtotime($status['updated_at']))
                    ->timezone($timezone)
                    ->toDateTimeString();
    
                $status['updated_at'] = Carbon::parse($get_current_date)->format('Y-m-d h:i:s a');
            } 
            array_push($new_data_array, $status);
        }

        $this->excelPdfStructure($report_type, $new_data_array, $list_of_headers);

    }

    private function excelPdfStructure($report_type, $data_array, $list_of_headers) {
        
        if ($report_type === 'excel') {
            
            $spreadsheet = new Spreadsheet();
        
            $sheet = $spreadsheet->getActiveSheet();

            $rowHeader = 6;
            $colHeader = 1;
            $colLetters = 'A';
            foreach($list_of_headers as $value) {
                $sheet->getStyleByColumnAndRow($colHeader, $rowHeader)->getFont()->setBold(true);
                $sheet->setCellValueByColumnAndRow($colHeader, $rowHeader, $value);
                $sheet->getColumnDimension($colLetters)->setAutoSize(true);
                $colHeader++;
                $colLetters++;
            }

            $row = 7;
            foreach($data_array as $value) {
                $col = 1;
                foreach($value as $specific_value) {
                    
                    $sheet->setCellValueByColumnAndRow($col, $row, $specific_value);
                    $col++;
                }
                $row++;
            }

            $sheet->mergeCells("A1:A5");

            $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            $drawing->setName('SentryCX');
            $drawing->setDescription('SentryCX');
            $drawing->setPath('angular2-logo-white.png'); 
            $drawing->setCoordinates('A1');
            $drawing->setWidthAndHeight(100, 100);
            $drawing->setWorksheet($sheet);

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Cache-Control: max-age=0');
            header('Cache-Control: max-age=1');
            header('Cache-Control: cache, must-revalidate');
            header('Pragma: public');
        
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
        } else {

            $spreadsheet = new Spreadsheet();
        
            $sheet = $spreadsheet->getActiveSheet();
            $rowHeader = 6;
            $colHeader = 1;
            $colLetters = 'A';
            foreach($list_of_headers as $value) {
                $sheet->getStyleByColumnAndRow($colHeader, $rowHeader)->getFont()->setBold(true);
                $sheet->setCellValueByColumnAndRow($colHeader, $rowHeader, $value);
                $sheet->getColumnDimension($colLetters)->setAutoSize(true);
                $sheet->getStyle("$colLetters$rowHeader")->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);
                $colHeader++;
                $colLetters++;
            }

            $row = 7;
            foreach($data_array as $value) {
                $col = 1;
                $dataColLetters = 'A';
                foreach($value as $specific_value) {
                    $sheet->setCellValueByColumnAndRow($col, $row, $specific_value);
                    $sheet->getStyle("$dataColLetters$row")->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);
                    $col++;
                    $dataColLetters++;
                }
                $row++;

            }

            $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            $drawing->setName('SentryCX');
            $drawing->setDescription('SentryCX');
            $drawing->setPath('angular2-logo-white.png'); 
            $drawing->setWidthAndHeight(100, 100);
            $drawing->setResizeProportional(true);
            $drawing->setOffsetX(10);
            $drawing->setOffsetY(3);    
            $drawing->setWorksheet($sheet);

            IOFactory::registerWriter('Pdf', \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf::class);

            header('Content-Type: application/pdf');
            header('Cache-Control: max-age=0');

            $writer = IOFactory::createWriter($spreadsheet, 'Pdf');
            $writer->save('php://output');
        }
    }

    public function getFilterBreakdown( $selected_filter, $conditions = []){

        DB::insert("SET sql_mode = ''");

        if ($selected_filter && $selected_filter !== 'clear'){

            $selected_data = self::SELECT_REF[$selected_filter]; 

            $query = DB::table('agent_connections as ac');
            
            $query = ConnectedAgentsQueryHelper::condition( $query , $conditions );

            $query->selectRaw("$selected_data as selected_column");
            $query->selectRaw("count($selected_data) as count_data");
            $query->selectRaw("SUM(CASE WHEN $selected_data is null or $selected_data = '' THEN 1 ELSE 0 END) AS null_values"); 

            $query->leftJoin('workstation_profile AS wp', function( $join ){
                $join->on( 'wp.worker_id', '=', 'ac.worker_id')
                ->on('wp.id', '=', DB::raw("(SELECT max(id) from workstation_profile WHERE workstation_profile.worker_id = ac.worker_id AND redflag_id=0)"));
            });
            
            $query->leftJoin('agent_media_device as amd', 'amd.worker_id', '=', 'ac.worker_id' );
            
            $query->where('is_admin', FALSE);

            $query->where('redflag_id', 0);

            $query->groupBy($selected_data);

            return $query->get();
        } else {
            return; 
        }

    }

    public function getRawDataForExcel($conditions = [], $headers = '')
    {
        $include_agent_name = array('agent_name');
            $list_of_headers = array();
            $list_of_data_selected = array();
            $fetch_exist = array();
            $all_headers = [
                'agent_name', 'host_name', 'connection','agent', 'position', 'location', 'account', 
                'manager', 'mtr_highest_avg', 'mtr_highest_loss', 'VLAN', 'DNS_1', 'DNS_2', 'subnet', 
                'ISP', 'download_speed', 'upload_speed', 'average_latency', 'packet_loss', 'jitter', 
                'mos', 'updated_at', 'Throughput_percentage', 'lob', 'programme_msa', 'job_profile',
                'supervisor_email_id', 'supervisor_full_name', 'media_device'
            ];

            $headers_to_array = explode(',', $headers);
            foreach($headers_to_array as $head) {
                array_push($include_agent_name, $head);
            }
        
            foreach($all_headers as $val) {
                if (in_array($val, $include_agent_name)) {
                    if ($val == 'media_device') {
                        array_push($fetch_exist, 'audio', 'mic', 'video');
                    } else {
                        array_push($fetch_exist, $val);
                    }
                }
                
            }

            // foreach($fetch_exist as $header) {
            foreach($fetch_exist as $header) {
                $header_name = self::HEADERS_REF[$header]; 
                $data_name = self::DATA_REF[$header]; 
                array_push($list_of_headers, $header_name);
                array_push($list_of_data_selected, $data_name);
            }
            
            $data_selected = implode(',',$list_of_data_selected);

            $query = DB::table('agent_connections as ac');

            $query = ConnectedAgentsQueryHelper::condition( $query , $conditions );
            $query->selectRaw($headers);

            $query->leftJoin('workstation_profile AS wp', function( $join ){
                $join->on( 'wp.worker_id', '=', 'ac.worker_id')
                ->on('wp.id', '=', DB::raw("(SELECT max(id) from workstation_profile WHERE workstation_profile.worker_id = ac.worker_id AND redflag_id=0)"));
            });

            $query->leftJoin('cnx_employees as wd', 'wd.employee_number', '=', 'ac.worker_id' );
            
            $query->leftJoin('agent_media_device as amd', 'amd.worker_id', '=', 'ac.worker_id' );
            
            $query->where('is_admin', FALSE);

            $query->where('redflag_id', 0);

            if( $this->sortBy )
                $query->orderBy( $this->sortBy, $this->sortOrder );
            else
                $query->orderBy('ac.id', 'desc');
            
            $data = $query->get();

            $data_array = json_decode(json_encode($data), true);

            return array('data_array' => $data_array, 'headers' => $list_of_headers);
    }

    public function getAgentAccounts($applications_type, $search = '') {

        $sub_day = Carbon::now()->subDay()->toDateTimeString();

        $applications = ApplicationsList::select('name')
            ->where('type', $applications_type)
            ->get();
        
        $accounts = AgentApplications::query()
            ->select('account')
            ->leftJoin('applications_list as al', 'al.id', '=', 'aa.application_id')
            ->where('aa.created_at', '>=', $sub_day)
            ->groupBy('account');

        if(!empty($search)) {
            $accounts->where('account', 'like', "%$search%");
        }

        foreach ($applications as $row) {
            $accounts->selectRaw("sum(`name` = ?) as `{$row->name}`", [$row->name]);
        }

        return $accounts->get();
    }    
    
    public function getLocationPerAccount($applications_type, $search = '') {

        $sub_day = Carbon::now()->subDay()->toDateTimeString();

        $applications = ApplicationsList::select('name')
            ->where('type', $applications_type)
            ->get();

        $accounts = AgentApplications::query()
            ->select('account', 'location')
            ->leftJoin('applications_list as al', 'al.id', '=', 'aa.application_id')
            ->where('aa.created_at', '>=', $sub_day)
            ->groupBy('account', 'location');

        if (!empty($search)) {
            $accounts->where('account', 'like', "%$search%");
        }

        foreach ($applications as $row) {
            $accounts->selectRaw("sum(`name` = ?) as `{$row->name}`", [$row->name]);
        }

        return $accounts->get()->groupBy('account');
    }

    public function getUsersPerAccount($applications_type) {
        $sub_day = Carbon::now()->subDay()->toDateTimeString();

        $applications = ApplicationsList::select('name')
            ->where('type', $applications_type)
            ->get();

        $accounts = AgentApplications::query()
            ->select('account', 'aa.location', DB::raw('CONCAT(firstname, " ", lastname) AS full_name'))
            ->leftJoin('applications_list as al', 'al.id', '=', 'aa.application_id')
            ->leftJoin('cnx_employees as ce', 'ce.employee_number', '=', 'aa.worker_id')
            ->where('aa.created_at', '>=', $sub_day)
            ->groupBy('account', 'aa.location', 'full_name');

        foreach ($applications as $row) {
            $accounts->selectRaw("sum(`name` = ?) as `{$row->name}`", [$row->name]);
        }

        return $accounts->get()->groupBy('account', 'aa.location');
    }

    public function getTotalApplicationCounts($applications_type, $search = '') {
        $applications = ApplicationsList::select('name')
            ->where('type', $applications_type)
            ->get();

        $total_counts = AgentApplications::query()
            ->leftJoin('applications_list as al', 'al.id', '=', 'aa.application_id')
            ->where('aa.created_at', '>=', Carbon::now()->subDay()->toDateTimeString());

        if (!empty($search)) {
            $total_counts->where('account', 'like', "%$search%");
        }

        foreach ($applications as $row) {
            $total_counts->selectRaw("sum(`name` = ?) as `{$row->name}`", [$row->name]);
        }

        return $total_counts->limit(1)->get();
    }

    public function sendEmailForApplications() {

        $sub_day = Carbon::now()->subDay()->toDateTimeString();

        $worker_id = AgentApplications::select('worker_id')
                    ->groupBy('worker_id')
                    ->where('updated_at', '>=', $sub_day)
                    ->get();

        foreach($worker_id as $value) {
            $data = AgentApplications::from('agent_applications as aa')
                    ->select('worker_id', 'account', 'aa.location as app_location', 'name', 'al.type as app_type', 'firstname', 'lastname')
                    ->leftJoin('applications_list as al', 'al.id', '=', 'aa.application_id')
                    ->leftJoin('cnx_employees as ce', 'ce.employee_number', '=', 'aa.worker_id')
                    ->where('worker_id', $value['worker_id'])
                    ->get()->toArray();

            // for temporary
            // $this -> sendEmailApplicationsData($data);

        }
    }

    private function sendEmailApplicationsData($data) {
        $required_apps = array();
        $restricted_apps = array();

        $sending_message = new \Symfony\Component\Console\Output\ConsoleOutput();

        $sending_message->writeln('Sending Email');
        
        foreach($data as $value) {
            if ($value['app_type'] == 'Required') {
                array_push($required_apps, $value['name']);
            } else {
                array_push($restricted_apps, $value['name']);
            }
        }

        $data_array = $data[0];
       
        if ($required_apps > 0) {
            $concat_required = implode(', ', $required_apps);
            $data_array['required_apps'] = $concat_required;
        }
        
        if ($restricted_apps > 0) {
            $concat_restricted = implode(', ', $restricted_apps);
            $data_array['restricted_apps'] = $concat_restricted;
        }

        Mail::to('julius.esclamado@concentrix.com')
            ->cc(['michael.vicente@concentrix.com', 'kurt.maderal@concentrix.com'])
            ->send(new AgentApplicationsMail($data_array));
    }

    private function checkOnlyZeroes($input)
    {
        return preg_match('/^[0]+$/', $input);
    }
}


