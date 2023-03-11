<?php

namespace App\Modules\WorkstationModule\Services;

use Illuminate\Support\Facades\DB;
use  App\Modules\WorkstationModule\Helpers\ConnectedAgentsQueryHelper;
use App\Modules\WorkstationModule\Helpers\GeoMappingQueryHelper;
use App\Modules\WorkstationModule\Models\AgentConnection;
use App\Modules\WorkstationModule\Models\AgentLocation;
use App\Modules\WorkstationModule\Models\VpnApproval;
use App\Modules\WorkstationModule\Models\RemarksList;
use App\Modules\WorkstationModule\Models\SecurecxUrlsStreaming;
use Log;

use Carbon\Carbon;
/**
 * Application Service
 */
class WorkstationService
{
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
        'code'          => 'ol.options'
    ];


    /** @var String $sortBy */
    protected $sortBy = null;
    protected $sortOrder = 'asc';

    public function setSort($field, $order)
    {
        $this->sortBy = $field;
        $this->sortOrder =  strtolower($order);
    }

    /**
     * Setup dependency injections here
     *
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct()
    {
    }


    /**
     *
     * Retrieve the count of the connected agents by account
     *
     * @param String $account
     * @return Int
     * @throws conditon
     **/
    public function countAgents($account)
    {
        return DB::table('agent_connections')
            ->where('is_active', TRUE)
            ->where('is_admin', FALSE)
            ->where('account', $account)
            ->count();
    }

    /**
     *
     * Retrieve the list of NOC agents socket IDs
     *
     * @return Array
     * @throws conditon
     **/
    public function getNOCSessions()
    {
        $connections = DB::table('agent_connections')
            ->select('session_id')
            ->where('is_active', TRUE)
            ->where('is_admin', TRUE)
            ->get();

        return $connections->map(function ($connection) {
            return $connection->session_id;
        })->flatten()->toArray();
    }


    /**
     * 
     * TODO : Move this to AgentConnectionService
     * Retrieve the list of applications
     *
     * @return type
     * @throws conditon
     **/
    public function sendMTR($data, $done_processing = false)
    {
        $timestamp_submitted = date("Y-m-d H:i:s");

        $updated_data = [
            'agent_name'            => $data['firstname'] . " " . $data['lastname'],
            'agent_email'           => $data['email'],
            'created_at'            => $timestamp_submitted,
            'worker_id'             => $data['worker_id'],
            'station_name'          => $data['station_number'],
            'location'              => $data['location'],
            'account'               => $data['account'],
            'country'               => $data['country'],
            'mtr_host'              => $data['mtr_host'],
            'mtr_highest_avg'       => $data['mtr_highest_avg'],
            'mtr_highest_loss'      => $data['mtr_highest_loss'],
            'mtr_result'            => $data['MTR'],
            'session_id'            => $data['session_id'],
            'is_active'             => TRUE,
            'is_admin'              => ($data['is_admin']  == 'Admin') ? true : false
        ];

        if ($done_processing)
            $updated_data['processing'] = FALSE;

        DB::table('agent_connections')->updateOrInsert(
            ['worker_id' => $data['worker_id']],
            $updated_data
        );
    }

    /**
     *
     * Tag the redflag as ongoing process or done
     *
     * @param Int $reflag_id
     * @param Boolean $done
     * @return type
     * @throws conditon
     **/
    public function processWorkstation($redflag_id = 0, $done = false)
    {
        return DB::table('redflag_dasbhoard')
            ->where('id', $redflag_id)
            ->update(['workstation_processing' => $done]);
    }

    /**
     * TODO : Refractor this and move into different class
     *       As well as the MTR progress process flow
     *       They should be different class but have the same functions
     *       So enforce interface concept
     *
     * Checks the progress of the workstation request
     *
     * @param Int $worker_id Employee ID
     * @param Int $type , 1 for MTR_REQUEST, 2 for WORKSTATION_PROFILE
     * @return type
     * @throws conditon
     **/
    public function getProgress($worker_id = 0, $type = 1)
    {
        return DB::table('progress_results')
            ->where('type', $type)
            ->where('worker_id', $worker_id)
            ->first();
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
    public function getWorkstation($conditions = [])
    {
        $query = DB::table('workstation_profile as wp');
        $query->leftJoin('agent_connections AS ac', 'ac.worker_id', '=', 'wp.worker_id');

        foreach ($conditions as $field => $value)
            $query->where($field, $value);

        $query->where('redflag_id', 0);
        $query->orderBy('wp.id', 'asc');

        $query->limit(1);

        return $query->get();
    }

    /**
     *
     * Retrieve the online workstation 
     *
     * @param String $version
     * @return type
     * @throws conditon
     **/
    public function getOnlineWorkstation($version)
    {
        $query = DB::table('workstation_profile as wp');
        $query->leftJoin('agent_connections AS ac', 'ac.worker_id', '=', 'wp.worker_id');

        $query->select('wp.worker_id', 'ac.session_id', 'ac.agent_name');
        $query->where('wp.desktop_app_version', '<>', $version);
        $query->where('redflag_id', 0);
        $query->where('is_active', true);
        $query->where('is_admin', false);
        $query->orderBy('ac.agent_name', 'asc');
        $query->distinct();
        return $query->get();
    }

    /**
     * TODO : Refractor this. Move this into
     *        AgentConnectionsService
     * Get Employee Session ID
     *
     * @param String $worker_id
     * @return type
     * @throws conditon
     **/
    public function getAgentSession($worker_id = 0)
    {
        $connection = DB::table('agent_connections')->where('worker_id', $worker_id)->first();

        return $connection->session_id;
    }

    /**
     * NOTE : Deprecated, will be removed and suggest to call from AgentConnectionService
     * 
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function getConnectedToc($page = 1, $conditions = [], $per_page = 20)
    {
        $query = DB::table('agent_connections as ac');

        $query = ConnectedAgentsQueryHelper::condition($query, $conditions);

        $query->where('is_admin', TRUE);

        $query->leftJoin('workstation_profile AS wp', function ($join) {
            $join->on('wp.worker_id', '=', 'ac.worker_id')
                ->on('wp.id', '=', DB::raw("(SELECT max(id) from workstation_profile WHERE workstation_profile.worker_id = ac.worker_id AND redflag_id=0)"));
        });

        $query->where('redflag_id', 0);

        return $query->paginate($per_page, ['*'], 'page', $page);
    }







    /**
     *
     * Retrieve the filters for search
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function getFilters()
    {
        return [
            'location'  => $this->_getDistinctValues('location'),
            'account'   => $this->_getDistinctValues('account'),
            'country'   => $this->_getDistinctValues('country'),
            'VLAN'      => $this->_getDistinctValues('VLAN', 'workstation_profile'),
            'DNS_1'     => $this->_getDistinctValues('DNS_1', 'workstation_profile'),
            'DNS_2'     => $this->_getDistinctValues('DNS_2', 'workstation_profile'),
            'subnet'    => $this->_getDistinctValues('subnet', 'workstation_profile'),
            'ISP'       => $this->_getDistinctValues('ISP', 'workstation_profile'),
            'agent_name' => $this->_getDistinctValues('agent_name')
        ];
    }

    public function getSecurecxFilters()
    {
        return [
            'location'  => $this->_getDistinctSecurecxValues('location'),
            'account'   => $this->_getDistinctSecurecxValues('account'),
            'agent_name' => $this->_getDistinctSecurecxValues('agent_name')
        ];
    }

    public function getGeoMappingFilters(){
        return [
            'account'   => $this->_getDistinctValues('account'),
            'country'   => $this->getDistinctData('country'),
            'city' => $this->getDistinctData('city'),
            'location' => $this->_getDistinctValues('location')
        ];
    }

    private function getDistinctData($column_name = ''){
        return AgentLocation::distinct()
        ->where($column_name, '!=', '')
        ->orderBy($column_name, 'ASC')
        ->get([$column_name])
        ->map(function ($item) use ($column_name) {
            return $item->$column_name;
        });
    }

    /**
     * Retrieve all the unique values for specific columns
     * which can then be used for filter referrence
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    private function _getDistinctValues($column_name = '', $table_name = 'agent_connections')
    {
        return DB::table($table_name)->distinct()
            ->where($column_name, '!=', '')
            ->orderBy($column_name, 'ASC')
            ->get([$column_name])
            ->where('is_admin', FALSE)
            ->map(function ($item) use ($column_name) {
                return $item->$column_name;
            });
    }

    private function _getDistinctSecurecxValues($column_name = '')
    {
        return AgentConnection::distinct()
            ->leftJoin('accounts', 'agent_connections.account', '=', 'accounts.name')
            ->where($column_name, '!=', '')
            ->where('accounts.has_securecx', true)
            ->orderBy($column_name, 'ASC')
            ->get([$column_name])
            ->where('is_admin', FALSE)
            ->map(function ($item) use ($column_name) {
                return $item->$column_name;
            });
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
    public function process($session_id = '', $start = TRUE, $mtr_host = '')
    {
        $update_data = ['processing' => $start];
        if ($mtr_host)
            $update_data['mtr_host'] = $mtr_host;
        return DB::table('agent_connections')
            ->where('session_id', $session_id)
            ->update($update_data);
    }

    /**
     * DEPRECATED : Use updateStatus on AgentConnectionService instead
     * Updates the status whether the connection is active or inactive
     *
     * @param String $session_id
     * @param Boolean $active
     * @return Boolean
     * @throws conditon
     **/
    public function updateStatus($session_id = '', $active = TRUE)
    {
        $update_data = ['is_active' => $active];
        if (!$active) {
            $timestamp = date("Y-m-d H:i:s");
            $update_data['last_logged_in'] = $timestamp;
        }

        return DB::table('agent_connections')
            ->where('session_id', $session_id)
            ->update($update_data);
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
    public function updateThresholdStatus($worker_id, $status = True)
    {
        return DB::table('agent_connections')
            ->where('worker_id', $worker_id)
            ->update(['has_threshold' => $status]);
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
    public function getAgent($connection_id = '', $type = 1)
    {
        return DB::table('agent_connections AS ac')
            ->leftJoin('progress_results AS pr', 'pr.worker_id', '=', 'ac.worker_id')
            ->addSelect('ac.*')
            ->addSelect('pr.progress')
            ->where('ac.id', $connection_id)
            // -> where('pr.type', $type)
            ->first();
    }

    /**
     * Retrieve the list of workstation profile by Flag
     *
     * @param int $flag_id
     * @param int $page_no
     * @return type
     * @throws conditon
     **/
    public function getProfile($flag_id, $page_no = 1)
    {
        $per_page = 1;

        $query = DB::table('workstation_profile AS wp')
            ->leftJoin('redflag_dashboard AS rd', 'rd.id', '=', 'wp.redflag_id')
            // -> leftJoin('hardware_info AS hi',  'hi.redflag_id' ,'=', 'rd.id', )
            ->leftJoin('agent_connections AS ac', 'ac.worker_id', '=', 'wp.worker_id')
            ->where('wp.redflag_id', $flag_id)
            ->orderBy('wp.date_created', 'desc');
        // $query = $query->whereHas('orders', function (Builder $query) use ($request) { $query = $query->where('orders.customer_id', 'NULL') );
        return $query->paginate($per_page, ['*'], 'page', $page_no);
    }

    /**
     * Retrieve the lezap of the workstation
     *
     * @param Array $condition Array of conditions such as [redflag_id, ]
     * @return type
     * @throws conditon
     **/
    public function getLezap($conditions = [])
    {
        $query =  DB::table('hardware_info');

        foreach ($conditions as $field => $value)
            $query->where($field, $value);

        return $query->first();
    }

    /**
     * Request for a new workstation profile
     *
     * @param $flag_id
     * @return type
     * @throws conditon
     **/
    public function request($flag_id = 0, $employee_id = 0)
    {
        return DB::table('request_update')
            ->updateOrInsert(
                ['redflag_id' => $flag_id, 'worker_id' => $employee_id],
                ['is_active' => TRUE]
            );
    }

    /**
     *
     * Tag the percentage of the progress
     *
     * @param String $worker_id Employee ID
     * @param Number $percentage Percentage , value should be 0 to 100
     * @param Number $type Type of progress, 1 = MTR_REQUEST, 2 = WORKSTATION_UPDATE
     * @return type
     * @throws conditon
     **/
    public function progress($worker_id = 0, $progress = 0, $type = 1)
    {
        $timestamp_submitted = date("Y-m-d H:i:s");
        $progress = trim($progress, '"');

        return DB::table('progress_results')
            ->updateOrInsert(
                ['worker_id' => $worker_id, 'type' => $type],
                ['progress' => (int)$progress, 'last_progress' => $timestamp_submitted, 'updated_at' => $timestamp_submitted]
            );
    }

    /**
     *
     * Update the Media Device status of agent
     * TODO : Already transferred this to MediaDeviceService
     *
     * @param String $worker_id
     * @param Array $stats
     * @return Boolean
     * @throws conditon
     **/
    public function updateMediaDevice($worker_id, $stats, $field = 'remarks')
    {
        $updated_data = array_merge($stats, ['worker_id' => $worker_id]);
        $updated_data[$field] = json_encode($updated_data[$field]);
        return DB::table('agent_media_device')
            ->updateOrInsert(
                ['worker_id' => $worker_id],
                $updated_data
            );
    }

    /**
     *
     * Retrieve the media device details
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function getMediaDevice($worker_id = 0)
    {
        $devices = DB::table('agent_media_device')->where('worker_id', $worker_id)->first();

        if (!$devices)
            return false;

        $devices->remarks = json_decode($devices->remarks);

        return $devices;
    }

    public function getMappingList($page = 1, $per_page = 20, $search = "", $conditions = [])
    {
        $query = AgentConnection::select('agent_name', 'agent_connections.worker_id', 'station_name', 'location', 'account','is_active', 'session_id','ISP','download_speed','upload_speed', 
                    'al.city as loc_current', 'al.region', 'al.country', 'al.country_code', 'al.neighbourhood', 'al.zip_code', 'al.latitude', 'al.longitude')
                ->leftJoin('workstation_profile AS wp', function ($join) {
                    $join->on('wp.worker_id', '=', 'agent_connections.worker_id')
                        ->on('wp.id', '=', DB::raw("(SELECT max(id) from workstation_profile WHERE workstation_profile.worker_id = agent_connections.worker_id AND redflag_id=0)"));
                })
                ->leftJoin('agent_location AS al', function ($join) {
                    $join->on('al.worker_id', '=', 'agent_connections.worker_id')
                        ->on('al.id', '=', DB::raw("(SELECT max(id) from agent_location WHERE agent_location.worker_id = agent_connections.worker_id)"));
                })
                ->addSelect(DB::raw("(SELECT city from agent_location as al WHERE al.worker_id = agent_connections.worker_id AND created_at <= '".Carbon::now()->addWeeks(-1)."' ORDER BY id DESC LIMIT 1) as loc_week"))
                ->addSelect(DB::raw("(SELECT longitude from agent_location as al WHERE al.worker_id = agent_connections.worker_id AND created_at <= '".Carbon::now()->addWeeks(-1)."' ORDER BY id DESC LIMIT 1) as week_long"))
                ->addSelect(DB::raw("(SELECT latitude from agent_location as al WHERE al.worker_id = agent_connections.worker_id AND created_at <= '".Carbon::now()->addWeeks(-1)."' ORDER BY id DESC LIMIT 1) as week_lat"))
                ->addSelect(DB::raw("(SELECT neighbourhood from agent_location as al WHERE al.worker_id = agent_connections.worker_id AND created_at <= '".Carbon::now()->addWeeks(-1)."' ORDER BY id DESC LIMIT 1) as week_neighbourhood"))
                ->addSelect(DB::raw("(SELECT zip_code from agent_location as al WHERE al.worker_id = agent_connections.worker_id AND created_at <= '".Carbon::now()->addWeeks(-1)."' ORDER BY id DESC LIMIT 1) as week_zip"))
                ->addSelect(DB::raw("(SELECT region from agent_location as al WHERE al.worker_id = agent_connections.worker_id AND created_at <= '".Carbon::now()->addWeeks(-1)."' ORDER BY id DESC LIMIT 1) as week_region"))
                ->addSelect(DB::raw("(SELECT country from agent_location as al WHERE al.worker_id = agent_connections.worker_id AND created_at <= '".Carbon::now()->addWeeks(-1)."' ORDER BY id DESC LIMIT 1) as week_country"))
                ->addSelect(DB::raw("(SELECT workstation_type from agent_location as al WHERE al.worker_id = agent_connections.worker_id AND created_at <= '".Carbon::now()->addWeeks(-1)."' ORDER BY id DESC LIMIT 1) as week_workstation_type"))
                ->where('is_admin', FALSE)
                ->orderBy('is_active', 'DESC');

        $query = GeoMappingQueryHelper::condition( $query , $conditions );
        
        if( $this->sortBy) {

            $query->orderBy( $this->sortBy, $this->sortOrder );

        }
        
        $query = $query->paginate($per_page, ['*'], 'page', $page);
        
        $active = AgentConnection::where(['is_active' => true, 'is_admin' => false]) -> count();
        return array('active' => $active, 'details' => $query);
    }

    public function getVpnApprovalData($page = 1 , $per_page = 20, $status = '', $search = '')
    { 
        $sub_day = Carbon::yesterday();

        $query = VpnApproval::query()
                ->where('status', $status)
                ->where('updated_at', '>=', $sub_day);

        if ($search != '') {
            $query->where('name', 'like', "%$search%");
        }

        return $query->paginate($per_page, ['*'], 'page', $page );
    }

    public function updateVpnApproval($params, $name) 
    {
        $data = null;

        $object_data = (object) $params;
        
        $current_date_time = Carbon::now()->toDateTimeString();

        $query = VpnApproval::where('id', $object_data->options_list_id )
            ->update([
                'status' => $object_data->params['status'],
                'action_taken_by' => $name,
                'action_taken_at' => $current_date_time,
                'action_taken_remarks' => $object_data->params['action_taken_remarks'],
                'updated_at' => DB::raw('updated_at')
            ]);

        if ($query) {
            $data = VpnApproval::where('id', $object_data->options_list_id )->first();
        }

        return $data;
    }

    public function vpnApprovalSave($data) 
    {
        $object_data = (object) $data;
        
        VpnApproval::updateOrCreate(
            ['worker_id' => $object_data->worker_id, 'status' => 'Pending'],
            [
                'workstation' => $object_data->workstation,
                'name' => $object_data->name,
                'email' => $object_data->email,
                'remarks' => $object_data->remarks
            ]
        );
    }

    public function deleteVpnApproval($data)
    {
        $object_data = (object) $data;

        VpnApproval::where('id', $object_data->options_list_id)->delete();
    }

    public function getRemarksData()
    {
        $query = RemarksList::query();

        return $query -> get();
    }

    public function saveSecurecxStreaming($data)
    {
        
        SecurecxUrlsStreaming::updateOrCreate(
            [
                'worker_id' => $data->worker_id
            ],
            [
                'streaming_primary_url' => $data->streaming_primary_url, 
                'streaming_primary_host_name' => $data->streaming_primary_host_name, 
                'streaming_primary_port' => $data->streaming_primary_port, 
                'streaming_primary_telnet_result' => $data->streaming_primary_telnet_result,
                'streaming_secondary_url' => $data->streaming_secondary_url, 
                'streaming_secondary_host_name' => $data->streaming_secondary_host_name, 
                'streaming_secondary_port' => $data->streaming_secondary_port, 
                'streaming_secondary_telnet_result' => $data->streaming_secondary_telnet_result,
                'status_primary_url' => $data->status_primary_url, 
                'status_primary_host_name' => $data->status_primary_host_name, 
                'status_primary_port' => $data->status_primary_port, 
                'status_primary_telnet_result' => $data->status_primary_telnet_result,
                'status_secondary_url' => $data->status_secondary_url, 
                'status_secondary_host_name' => $data->status_secondary_host_name, 
                'status_secondary_port' => $data->status_secondary_port, 
                'status_secondary_telnet_result' => $data->status_secondary_telnet_result
            ]
        );
    }
    
    public function getVPNRequestListData($worker_id)
    {
        $query = VpnApproval::where('worker_id', $worker_id);

        return $query -> get();
    }

    public function updateVPNStatusPendingToExpired()
    {
        Log::info('START CRON UPDATE STATUS FROM PENDING TO EXPIRED '.Carbon::now()->toDateTimeString());

        $sub_day = Carbon::now()->subDay()->toDateTimeString();

        VpnApproval::where('status', 'Pending')
            ->where('updated_at', '<=', $sub_day)
            ->update(['status' => 'Expired']);

        Log::info('END CRON UPDATE STATUS FROM PENDING TO EXPIRED '.Carbon::now()->toDateTimeString());

    }
    
    public function checkPercentage($percentage, $worker_id) {
        if ((int)$percentage === 100) {
           return AgentConnection::where('worker_id', $worker_id)
                ->update(['processing' => false]);
        }
    }
}
