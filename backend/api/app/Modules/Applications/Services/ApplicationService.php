<?php

namespace App\Modules\Applications\Services;

use Illuminate\Support\Facades\DB;
use App\Modules\Applications\Models\VLAN;
use App\Modules\Applications\Models\IspReference;
use App\Modules\Applications\Models\AgentNetwork;
use App\Modules\Applications\Models\ApplicationsList;
use App\Modules\Applications\Models\AgentApplications;
use App\Modules\Applications\Models\AgentLocation;
use App\Modules\Applications\Models\HardwareInfo;
use App\Modules\Applications\Models\AgentConnection;
use App\Modules\Applications\Models\Monitoring;
use App\Modules\Applications\Models\MonitoringPing;
use App\Modules\Applications\Models\MonitoringTraceroute;
use App\Modules\HistoricalRecords\Services\SpeedtestRecordsService;
use App\Modules\HistoricalRecords\Services\WorkstationProfileRecordsService;
use App\Modules\HistoricalRecords\Services\MeanOpinionScoreRecordsService;
use App\Modules\WorkstationModule\Models\WorkstationProfile;
use App\Modules\Maintenance\Models\TimeFrame;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

/**
 * Application Service
 */
class ApplicationService
{
    /** @var MeanOpinionScoreRecordsService $var description */
    protected $mosLog;

    /** @var SpeedtestRecordsService $speedtestLog description */
    protected $speedtestLog;

    /** @var WorkstationProfileRecordsService $workstationLog description */
    protected $workstationLog;

	/**
	 * DB read connection
	 */
	protected $DB_READ;

    /**
     * Setup dependency injections here
     *
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct(
            SpeedtestRecordsService $speedtestLog, 
            WorkstationProfileRecordsService $workstationLog,
            MeanOpinionScoreRecordsService $mosLog
    ){
        $this -> speedtestLog = $speedtestLog;
        $this -> workstationLog = $workstationLog;
        $this -> mosLog = $mosLog;

		$this->DB_READ = config('dbreadwrite.db_read');
    }


    /**
     * Retrieve the list of applications
     *
     * @return type
     * @throws conditon
     **/
    public function getApplications($account_id)
    {
        return DB::table('applications')
                         ->where('account', urldecode($account_id))
                         ->where('is_loaded', 1)
                         ->orderBy('name')->get();
    }

    /**
     * Retrieve the list of applications
     * TODO : Check with abel if the getApplications() is working, since there's not account_id currently
     *
     * @return type
     * @throws conditon
     **/
    public function getApplicationsByID($account_id)
    {
        return $results = DB::table('applications')->where('account_id', $account_id)->orderBy('name')->get();
    }
    /**
     * Retrieve the list of networks
     *
     * @return type
     * @throws conditon
     **/
    public function getNetworks($account_id)
    {
        return $results = DB::table('networks')->where('account', $account_id)->orderBy('url')->get();
    }
    /**
     * Retrieve the list of Categories
     *
     * @return type
     * @throws conditon
     **/
    public function getCategories($account)
    {
        // return $results = DB::table('category')->orderBy('name')->get();
        return $results = DB::table('codes_per_account')
            ->join('options_list', 'codes_per_account.options_list_id', '=', 'options_list.id')
            ->join('category', 'options_list.category_id', '=', 'category.id')
            ->select('category.*')
            ->where('codes_per_account.account', $account)
            ->distinct()->orderBy('name')->get();

    }
    /**
     * Retrieve the list of Codes per category
     *
     * @return type
     * @throws conditon
     **/
    public function getCodeList($account, $id)
    {
        // return $results = DB::table('options_list')->where('category_id', $id)->orderBy('options')->get();
        return $results = DB::table('codes_per_account')
        ->join('options_list', 'codes_per_account.options_list_id', '=', 'options_list.id')
        ->select('options_list.*')
        ->where('codes_per_account.account', $account)
        ->where('options_list.category_id', $id)
        ->distinct()->orderBy('options')->get();
    }
    /**
     * Retrieve the Workday Profile
     * TODO : Use WorkdayService Instead
     *
     * @return type
     * @throws conditon
     **/
    public function getWorkdayProfile($username)
    {
        $email = $username."@concentrix.com";

        $sam_account = $username."@";

        $field_names = [
            "emp.employee_number",
            "emp.firstname",
            "emp.lastname",
            "emp.email",
            "emp.location_name",
            "emp.job_profile",
            "emp.programme_msa",
            "emp.supervisor_id",
            "emp.supervisor_email_id",
            "emp.supervisor_full_name",
            "emp.msa_client",
            "emp.lob",
            "emp.country"
            // "users.id AS isAdmin"
        ];

        $whitelisted_users = getenv('WHITELISTED_USERS');
        $whitelisted_msa_client = getenv('WHITELISTED_MSA_CLIENT');
    
        $query = DB::connection($this->DB_READ)->table('cnx_employees as emp');
        $query -> leftJoin('users', 'emp.email', '=', 'users.email');
        $query -> leftJoin('agent_connections as ac', 'emp.employee_number', '=', 'ac.worker_id');

        foreach( $field_names as $field_name ) 
            $query -> addSelect( $field_name );
        
        $query ->selectRaw(DB::raw("COALESCE(ac.is_disabled, false) AS is_disabled"));

        $query -> selectRaw(DB::raw("case when ac.id then false else true end as first_time_user"));    

        // $query -> selectRaw(DB::raw("case when emp.msa_client = ${whitelisted_msa_client} or emp.email in (${whitelisted_users}) or users.id then true else false end as isAdmin"));

        $query -> selectRaw(DB::raw("case
            WHEN emp.email IN (${whitelisted_users}) THEN NULL
            WHEN emp.msa_client = ${whitelisted_msa_client} THEN TRUE
            WHEN users.id THEN TRUE
            ELSE NULL
            END as isAdmin"));

        return $query -> where('emp.email', $email)
            -> orWhere('sam_account_name', $username)
            -> orWhere('sam_account_name', 'like', "$sam_account%")
            -> orWhere('cnx_lan_id', $username)
            -> get();
            // -> toSql();
    }
    /**
     * Retrieve the Account ID
     *
     * @return type
     * @throws conditon
     **/
    public function getAccountId($account)
    {
        return $results = DB::table('accounts')->where('name', $account)->get();
    }
    /**
     * Retrieve the IP and URL from selected combobox on desktop app
     *
     * @return type
     * @throws conditon
     **/
    public function getIPURL($value, $table, $account)
    {
        $value = urldecode($value);
        if($table == "applications")
            return $results = DB::table($table)->where('name', $value)->where('account', $account)->get();
        else
            return $results = DB::table($table)->where('url', $value)->where('account', $account)->get();
    }
    /**
     * Submit Redflag and Workstation info
     *
     * @return type
     * @throws conditon
     **/
    public function submitCode($data)
    {
		$entry = (object) $data;

        $username = str_replace('-', '.', $entry->username);
        if($entry->red_flag_id == "0")
        {
            $id = DB::table('redflag_dashboard')->insertGetId(
                [
                    'code_id'               => (int) $entry->code_id,
                    'agent_username'        => $username,
                    'agent_name'            => "{$entry->firstname} {$entry->lastname}",
                    'lob'                   => $entry->lob,
                    'timestamp_submitted'   => DB::raw('now()'),
                    'status_info'           => 'Inquiry',
                    'account'               => $entry->account,
                    'location'              => $entry->location,
                    'worker_id'             => $entry->worker_id,
                    'country'               => $entry->country,
                    'ref_no'                => ( (array) $entry ) [0]['ref_no']
                ]
            );
            DB::table('workstation_profile')->insert(
                [
                    'redflag_id' => $id,
                    'worker_id' => $entry->worker_id,
                    'selected_ip' => "Fetching...",
                    'selected_host' => "Fetching...",
                    'host_name' => "Fetching...",
                    'host_ip_address' => "Fetching...",
                    'subnet' => "Fetching...",
                    'gateway' => "Fetching...",
                    'VLAN' => "Fetching..." ,
                    'DNS_1' => "Fetching...",
                    'DNS_2' => "Fetching...",
                    'station_number' => "Fetching...",
                    'ping' => "Fetching...",
                    'ping_ref' => "Fetching...",
                    'tracecert' => "Fetching...",
                    'tracecert_ref' => "Fetching...",
                    'host_file' => "Fetching...",
                    'ISP' => "Fetching...",
                    'isp_fullname' => "Fetching...",
                    'download_speed' => "Fetching...",
                    'upload_speed' => "Fetching...",
                    'mtr' => "Fetching...",
                    'network_adapter'       => "Fetching...",
                    'pac_address'       => "Fetching...",
                    'ram'       => "Fetching...",
                    'network_type' => "Fetching...",
                    'ram_usage' => "Fetching...",
                    'disk' => "Fetching...",
                    'free_disk' => "Fetching...",
                    'cpu' => "Fetching...",
                    'cpu_util' => "Fetching...",
                    'desktop_app_version' => "Fetching...",
                    'Current_lan_speed' => "Fetching...",
                    'Theoretical_Throughput' => "Fetching...",
                    'Maximum_Possible_Throughput' => "Fetching...",
                    'Throughput_percentage' => "Fetching...",
                    'Telnet80' => "Fetching...",
                    'Telnet443' => "Fetching..."
                ]
            );
            return $id;
        }
        $id = $entry->red_flag_id;
        if($entry->Need_WP == "Yes")
        {
            $host_name 		= str_replace('-', '.', $entry->host_name);
            $network_type	= $this->getNetworkType($entry->host_ip_address);
	    $isp_fullname	= !empty($entry->ISP) ? $entry->ISP : "-" ;
            $entry->ISP 	= $this->_getISPCode( $entry->ISP );
            
            $this->logSpeedtest($data);
            $this->logWorkstationProfile($data);

            DB::table('workstation_profile')->updateOrInsert(['redflag_id' => $id ],
                [
                    'redflag_id' 					=> $id,
                    'worker_id'						=> $entry->worker_id,
                    'selected_ip'					=> $entry->selected_ip,
                    'selected_host'					=> $entry->selected_host,
                    'host_name'						=> $host_name,
                    'host_ip_address'				=> $entry->host_ip_address,
                    'subnet'						=> $entry->subnet,
                    'gateway'						=> $entry->gateway,
                    'VLAN'							=> $this->_getVLAN( $entry->subnet ),
                    'DNS_1'							=> $entry->DNS_1,
                    'DNS_2'							=> $entry->DNS_2,
                    'station_number'				=> $entry->station_number,
                    'ping'							=> $entry->ping,
                    'ping_ref'						=> $entry->ping_ref,
                    'tracecert'						=> $entry->tracecert,
                    'tracecert_ref'					=> $entry->tracecert_ref,
                    'host_file'						=> $entry->host_file,
                    'ISP'							=> $entry->ISP,
                    'isp_fullname'					=> $isp_fullname,
                    'download_speed'				=> $entry->download_speed,
                    'upload_speed'					=> $entry->upload_speed,
                    'mtr'							=> $entry->MTR,
                    'network_adapter'				=> $entry->network_adapter,
                    'pac_address'					=> $entry->pac_address,
                    'ram'							=> $entry->ram,
                    'packet_loss'					=> $entry->packet_loss,
                    'average_latency'				=> $entry->average_latency,
                    'jitter'						=> $entry->jitter,
                    'mos'							=> $entry->mos,
                    'network_type'					=> $network_type,
                    'ram_usage'						=> $entry->ram_usage,
                    'disk'							=> $entry->disk,
                    'free_disk'						=> $entry->free_disk,
                    'cpu'							=> $entry->cpu,
                    'cpu_util'						=> $entry->cpu_util,
                    'desktop_app_version'			=> $entry->desktop_app_version,
                    'Current_lan_speed'				=> $this->_extractFromData( $data, 'Current_lan_speed' ),
                    'Theoretical_Throughput'		=> $this->_extractFromData( $data, 'Theoretical_Throughput' ),
                    'Maximum_Possible_Throughput'	=> $this->_extractFromData( $data, 'Maximum_Possible_Throughput' ),
                    'Throughput_percentage'			=> $this->_extractFromData( $data, 'Throughput_percentage' ),
                    'Telnet80'						=> $this->_extractFromData( $data, 'Telnet_port80' ),
                    'Telnet443'						=> $this->_extractFromData( $data, 'Telnet_port443' ),
                    'net_type'						=> $this->_getNetorkType( $data['subnet'] )
                ]
            );
        }
        return $id;
    }

    /**
     *
     * Checks the array if the propery exists, if null, return null as well
     * This avoid getting index error
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    private function _extractFromData( $data, $property_name ){
        if( !array_key_exists( $property_name , $data ) ) 
            return '';

        return $data[ $property_name ];
    }

    /**
     *
     * Log WOrkstation profile
     *
     * @param Array $data
     * @return type
     * @throws conditon
     **/
    public function logWorkstationProfile( $data ){
        $fields = $this -> workstationLog -> getFields();		
        $params = Arr::only($data, $fields);

		// add public_info
		try{
			$public = (object) $data;
			$public_info = (object) $public->public_info;
	
			$params['public_ip'] 	= $public_info->public_ip;
			$params['country'] 		= $public_info->country_isp;
			$params['region'] 		= $public_info->region;
			$params['city']			= $public_info->city;
			$params['zip_code']		= $public_info->zip_code;
			$params['latitude']		= $public_info->latitude;
			$params['longitude']	= $public_info->longitude;
            $params['DISK']         = $public->disk;
			
			
			$this -> workstationLog -> setWorkerID( $data['worker_id'] );
			$this -> workstationLog -> log( $params );

		}catch(Exception $e){
			Log::error(__FUNCTION__ . "\t" .  $e->getMessage());
			Log::error(print_r($data, 1));
		}
    }


    /**
     *
     * Log Speedtest Record
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function logSpeedtest($data){
        $this -> speedtestLog -> setWorkerID( $data['worker_id']);
        $this -> speedtestLog -> log( [
            'download_speed' => $data['download_speed'], 
            'upload_speed'   => $data['upload_speed'], 
            'connection_type' => $this -> getNetworkType( $data['host_ip_address']),
            'speedtest_source' => $data['speedtest_source']
        ]);
    }

    /**
     *
     * Logs the MOS data for historical record
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function logMeanOpinionScore( $data ){
        $this -> mosLog -> setWorkerID( $data['worker_id'] );

        $this -> mosLog -> log([
            'jitter'      => $data['jitter'],
            'average_latency' => $data['average_latency'],
            'mos'         => $data['mos'],
            'packet_loss' => $data['packet_loss']
        ]);
    }

    /**
     *
     * Retrieve the VLAN based on the subnet
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function _getVLAN( $subnet ){
        $vlan = "-";

        $check = VLAN::where('subnet', $subnet ) -> first();

        if( $check )
            $vlan = $check -> name;

        return $vlan;
    }


     /**
     * Submit Workstation info
     *
     * @return type
     * @throws conditon
     **/
    public function submitWorkstation( $data ) {
        $redflag_id = 0;
        if( array_key_exists('redflag_id', $data))
            $redflag_id = $data['redflag_id'];
                
        $workstation = collect($data);

        $host_name = str_replace('-', '.', $data['host_name']);
        $details = (object) $data;
	$network_type = $this -> getNetworkType($data['host_ip_address']);
	$isp_fullname	= !empty($data['ISP']) ? $data['ISP'] : "-" ;
        $data['ISP'] = $this->_getISPCode( $data['ISP'] );
//        $device_location = $data['Device_Location'] ?? [];

        $workstation_profile_data = [
            'redflag_id' => $redflag_id,
            'worker_id' => $details -> worker_id,
            'selected_ip' => $workstation->get('selected_ip'),
            'selected_host' => $workstation->get('selected_host'),
            'host_name' => $host_name,
            'host_ip_address' => $details -> host_ip_address,
            'subnet'  => $details -> subnet,
            'gateway' => $details -> gateway,
            'VLAN'    => $details -> VLAN,
            'DNS_1'   => $details -> DNS_1,
            'DNS_2'   => $details -> DNS_2,
            'station_number' => $details -> station_number,
            'ping'      => $details -> ping,
            'ping_ref'  => $details -> ping_ref,
            'tracecert' => $details -> tracecert,
            'tracecert_ref' => $details -> tracecert_ref,
            'host_file' => $details -> host_file,
            'ISP'       => $details -> ISP,
            'isp_fullname' => $isp_fullname,
            'download_speed' => $details -> download_speed,
            'upload_speed'   => $details -> upload_speed,
            'mtr'       => $details -> MTR,
            'network_adapter'       => $details -> network_adapter,
            'pac_address'       => $details -> pac_address,
            'ram'       => $details -> ram,
            'packet_loss'       => $details -> packet_loss,
            'average_latency'       => $details -> average_latency,
            'jitter'       => $details -> jitter,
            'mos'       => $details -> mos,
            'network_type' => $network_type,
            'ram_usage' => $details -> ram_usage,
            'disk' => $details -> disk,
            'free_disk' => $details -> free_disk,
            'cpu' => $details -> cpu,
            'cpu_util' => $details -> cpu_util,
            'desktop_app_version' => $details -> desktop_app_version,
            'Theoretical_Throughput' => $this -> _extractFromData( $data, 'Theoretical_Throughput' ),
            'Maximum_Possible_Throughput' => $this -> _extractFromData( $data, 'Maximum_Possible_Throughput' ),
            'Throughput_percentage' => $this -> _extractFromData( $data, 'Throughput_percentage' ),
            'net_type' => $this -> _getNetorkType( $details -> subnet ),
            'user_type' => isset($details -> user_type)? $details -> user_type : ""
        ];

//	Log::info(print_r($workstation_profile_data,1));

        if( $data['is_disabled'] == 'false' ){
            $this -> logSpeedtest($data);
            $this -> logWorkstationProfile( $data );
            $this -> logMeanOpinionScore( $data );
        }

//        $this -> saveDeviceLocation($device_location, $data['worker_id']);

        if( $redflag_id )
            return DB::table('workstation_profile')->insert( $workstation_profile_data );

        DB::table('workstation_profile')->updateOrInsert(
                ['worker_id' => $data['worker_id'], 'redflag_id' => 0 ],
                $workstation_profile_data 
        );

    }

    public function saveDeviceLocation($details, $worker_id)
    {
        $location_details = array(
            'worker_id' => $worker_id,
            'country'   => $details['country'],
            'country_code' => $details['country_code'],
            'neighbourhood' => $details['neighbourhood'],
            'region' => $details['region'],
            'city' => $details['city'],
            'zip_code' => $details['postcode'],
            'latitude' => $details['latitude'],
            'longitude' => $details['longitude'],
            'workstation_type' => $details['workstation_type']
        );

        return AgentLocation::Insert($location_details);

    }

    private function getNetworkType($host_ip_address)
    {
        $arr = explode(".", $host_ip_address, 2);
        $first_octet = $arr[0];
        $network_type = "WAH";
        if($first_octet == "10")
            $network_type = "WAH/VPN";
        return $network_type;
    }
    /**
     * Retrieve the Pending red flag per user
     *
     * @return type
     * @throws conditon
     **/
    public function getPendingFlags($worker_id)
    {
        // $worker_id = 'abel.go';
            return $results = DB::table('redflag_dashboard')
            ->select(
                    'redflag_dashboard.id',
                    'options_list.options' ,
                    'redflag_dashboard.timestamp_submitted',
                    'redflag_dashboard.timestamp_acknowledged',
                    'redflag_dashboard.timestamp_closed',
                    'redflag_dashboard.status_info'
                    )
            ->join('options_list','redflag_dashboard.code_id','=','options_list.id')
            ->where(['redflag_dashboard.worker_id' => $worker_id])
            ->where('redflag_dashboard.status_info', '<>', 'Closed')
            ->get();
    }
     /**
     * Confimr flag on agent's end
     *
     * @return type
     * @throws conditon
     **/
    public function closeFlag($id)
    {
        $date_now = date("Y-m-d H:i:s");
        DB::table('redflag_dashboard')
        ->where('id', $id)
        ->update(array('timestamp_closed' => $date_now, 'status_info' => 'Closed'));
    }
    /**
     * Retrieve the Pending red flag per user
     *
     * @return type
     * @throws conditon
     **/
    public function checkFlagStatus($worker_id)
    {
        // $worker_id = 'abel.go';
            return $results = DB::table('redflag_dashboard')
            ->select(
                    'redflag_dashboard.status_info'
                    )->distinct()
            ->where(['redflag_dashboard.worker_id' => $worker_id])
            ->get();
    }
    /**
     * Submit Hardware Info
     *
     * @return type
     * @throws conditon
     **/
    public function submitHardwareInfo( $data ) {
        $object_data = (object) $data;

        HardwareInfo::updateOrCreate(
            ['worker_id'         => $object_data->worker_id],
            [
                'station_number'    => $object_data->station_number,
                'gpu'               => $object_data->gpu,
                'disk_drive'        => $object_data->disk_drive,
                'processor'         => $object_data->processor,
                'os'                => $object_data->os,
                'network_interface' => $object_data->network_interface,
                'sound_card'        => $object_data->sound_card,
                'printer'           => $object_data->printer,
                'monitor'           => $object_data->monitor,   
                'camera'            => $object_data->camera,
                'keyboard'          => $object_data->keyboard,
                'mouse'             => $object_data->mouse,
                'installed_apps'    => $object_data->installed_apps['data'] ?? null,
                'ram'               => $object_data->total_ram,
                'memory'            => $object_data->available_ram,
                'mother_board'      => $this -> _extractFromData($data, 'mother_board')
            ]
        );
    }

    public function submitAgentApplications($data) {

        $new_applications = array();
        $fetch_specific_data = array();
        $object_data = (object) $data;
        $user_account = $object_data->installed_apps['info']['account'] ?? null;
        $user_location = $object_data->installed_apps['info']['location'] ?? null;
        $user_installed_applications = $object_data->installed_apps['details'] ?? null;
        $worker_id = $object_data->worker_id;
        
        $get_applications = ApplicationsList::query()
            ->where('account_affected', null)
            ->orWhere('account_affected', 'like', "%$user_account%")
            ->get();

        AgentApplications::where('worker_id', $worker_id)->delete();
        
        $end = end($user_installed_applications);

        foreach($get_applications as $apps) {
            foreach($user_installed_applications as $value) {
                if ((strtolower($value['name']) !== strtolower($apps['name'])  && $apps['type'] == 'Required' && strtolower($end['name']) === strtolower($value['name'])) ||
                    (strtolower($value['name'])) === strtolower($apps['name']) && $apps['type'] == 'Restricted') {
                    $fetch_specific_data['worker_id'] = $worker_id;
                    $fetch_specific_data['application_id'] = $apps['id'];
                    $fetch_specific_data['account'] = $user_account;
                    $fetch_specific_data['location'] = $user_location;
                    $fetch_specific_data['installed_date'] = $value['install_date'];
                    AgentApplications::create($fetch_specific_data);
                    break;
                    
                } else if (strtolower($value['name']) === strtolower($apps['name'])  && $apps['type'] == 'Required' ) {
                    break;
                }
            }
        }

    }

    public function StartUpRequirementsPerAccount($account)
    {
        return $results = DB::table('accounts')
        ->where('name', $account)->get();
    }

    public function _getISPCode( $ispName ) {
        $explode_isp = explode(' ', $ispName);
        $arr = array();
        $isp = $ispName;

        $fetchIspCode = IspReference::where('isp_name', $ispName ) -> first();
        
        if( $fetchIspCode ) {
            $isp = $fetchIspCode -> isp_code;
        } else if (!$fetchIspCode && count($explode_isp) < 3) {
            $isp = $explode_isp[0];
        } else {
            foreach( $explode_isp as $value ){
                array_push($arr, ucfirst(substr($value, 0, 1)));
            }
            $isp = implode('',$arr);
        }
        return $isp;
    }

    public function logSmartMonitoring($new_smart_monitoring_logs) {
        $batch_ping_data = array();
        $batch_traceroute_data = array();
        $batch_mtr_data = array();

        foreach ($new_smart_monitoring_logs as $value) {
            $worker_id = isset($value['worker_id']) ? $value['worker_id'] : null;
            $monitoring_model = Monitoring::create([
                'worker_id' => $worker_id,
                'workstation_id' => isset($value['workstation_id']) ? $value['workstation_id'] : null,
                'application' => isset($value['application']) ? $value['application'] : null,
                'type' => isset($value['type']) ? $value['type'] : null,
                'ram' => isset($value['ram']) ? $value['ram'] : null,
                'memory' => isset($value['memory']) ? $value['memory'] : null,
                'average_latency' => isset($value['average_latency']) ? $value['average_latency'] : null,
                'packet_loss' => isset($value['packet_loss']) ? $value['packet_loss'] : null,
                'jitter' => isset($value['jitter']) ? $value['jitter'] : null,
                'mos' => isset($value['mos']) ? $value['mos'] : null,
            ]);

            if (isset($value['ping'])) {
                foreach ($value['ping'] as $ping_value) {
                    $ping_value['monitoring_id'] = $monitoring_model->id;
                    $ping_value['created_at'] = \Carbon\Carbon::now();
                    $ping_value['updated_at'] = \Carbon\Carbon::now();
                    array_push($batch_ping_data, $ping_value);
                }
            }

            if (isset($value['traceroute'])) {
                foreach ($value['traceroute'] as $traceroute_value) {
                    $traceroute_value['monitoring_id'] = $monitoring_model->id;
                    $traceroute_value['created_at'] = \Carbon\Carbon::now();
                    $traceroute_value['updated_at'] = \Carbon\Carbon::now();
                    
                    if($traceroute_value['is_mtr'] === false)
                        array_push($batch_traceroute_data, $traceroute_value);
                    else
                        array_push($batch_mtr_data, $traceroute_value);   
                }
            }
        }

        $this->logMonitoringPing($batch_ping_data);
        $this->logMonitoringTraceroute($batch_traceroute_data);
        $this->logMonitoringTraceroute($batch_mtr_data);
    }

    public function logMonitoringPing($batch_ping_data) {
        if(count($batch_ping_data) > 0) MonitoringPing::insert($batch_ping_data);
    }

    public function logMonitoringTraceroute($batch_data) {
        if(count($batch_data) > 0) MonitoringTraceroute::insert($batch_data);
    }

    /**
     *
     * Retrieve the VLAN based on the subnet
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function _getNetorkType( $subnet ){
        
        $details = DB::Table('agent_network')-> where('subnet', $subnet ) -> first();
        
        if( $details )
        {
            $type = $details -> type;
        } else if (strpos($subnet, ".") !== false){
            $subnet_breakdown = explode(".", $subnet, 4);
            $first_octet = $subnet_breakdown[0];
            $second_octet = $subnet_breakdown[1];
            $third_octet = $subnet_breakdown[2];
            
            $subnet_data = $first_octet.".".$second_octet.".".$third_octet;
            
            $check_data = AgentNetwork::where('subnet', 'like', '%'.$subnet_data.'%') -> first();
            
            if ($check_data) {
                $type = $check_data -> type;
            } else {
                $type = $this->getNetworkType($subnet);
            }
        } else {
            $type = $this->getNetworkType($subnet);
        }

//        return 'B&M';
         return $type;
    }

    public function updateSessionId($data) {
        $object_data = (object) $data;

        if ($object_data->worker_id && $object_data->session_id) {
            AgentConnection::updateOrCreate(
                ['worker_id' => $object_data->worker_id], 
                ['session_id' => $object_data->session_id]
            );
        }
    }

}
