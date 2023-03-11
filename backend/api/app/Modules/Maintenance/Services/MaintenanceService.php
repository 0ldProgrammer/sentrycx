<?php

namespace App\Modules\Maintenance\Services;

use Illuminate\Support\Facades\DB;
use App\Modules\Maintenance\Models\AuxList;
use App\Modules\Maintenance\Models\AuxPerAccount;
use App\Modules\Maintenance\Models\DesktopApplicationList;
use Carbon\Carbon;
use App\Modules\Maintenance\Models\ApplicationsList;
use App\Modules\Maintenance\Models\CnxEmployees;
use App\Modules\Maintenance\Models\AgentApplications;
use App\Modules\Maintenance\Models\UserSoftwareUpdate;
use App\Modules\Maintenance\Models\LogsSoftwareUpdate;
use App\Modules\Maintenance\Models\AgentConnection;
use App\Modules\Maintenance\Models\ReportTypePerUser;
use App\Modules\Maintenance\Models\SmartReportType;
use App\Modules\Maintenance\Models\TimeFrame;

use Illuminate\Support\Facades\Redis;
use App\Modules\Maintenance\Models\Applications;
use App\Modules\Maintenance\Resources\DesktopApplicationListResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Console\Application;

use App\Modules\Maintenance\Models\AgentTheme;

/**
 * Application Service
 */
class MaintenanceService 
{
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
     * Retrieve the list of accounts
     *
     * @return type
     * @throws conditon
     **/
    public function getAccountList()
    {
        return $results = DB::table('accounts')-> where( 'is_active', 1 )->orderBy('name')->get();
    }

    /**
     * Retrieve the list of codes
     *
     * @return type
     * @throws conditon
     **/
    public function getCodeList($page = 1, $per_page = 20, $account = 0, $search = "" )
    {

        $query = DB::table('options_list as ol');
        $query -> leftJoin('category as cat','ol.category_id', '=', 'cat.id');
        $query -> addSelect("ol.id AS id");
        $query -> addSelect("ol.options AS options");
        $query -> addSelect("cat.name AS category");
        $query -> addSelect("cat.id AS category_id");
           $query -> addSelect(DB::raw("(
            SELECT 
            CASE
                WHEN b.options_list_id IS NULL THEN 'Inactive'
                ELSE 'Active'
            END AS QuantityText
            FROM `codes_per_account` b WHERE b.options_list_id = ol.id AND b.account = '$account'
        ) AS status_code"));
        $query -> orderBy('cat.name');
        $query -> orderBy('ol.options');
        $query -> where('ol.is_active', 1);
        if($search != "")
            $query ->where('ol.options', 'like', "%$search%");
        return $query -> paginate($per_page, ['*'], 'page', $search ? 1 : $page );
    }

    /**
     * Retrieve the list of aux
     *
     * @return type
     * @throws conditon
     **/
    public function getAuxList($page = 1, $per_page = 20, $account = 0, $search = "" )
    {
        $query = AuxList::select(DB::raw("(
            SELECT 
            CASE
                WHEN apa.aux_list_id IS NULL THEN 'Inactive'
                ELSE 'Active'
            END AS QuantityText
            FROM `aux_per_account` apa WHERE apa.aux_list_id = aux_list.id AND apa.account = '$account'
        ) AS aux_status, name, aux_list.id"));
        if($search != "")
            $query ->where('name', 'like', "%$search%");
        return $query -> paginate($per_page, ['*'], 'page', $page );
    }

    public function getAuxData()
    {
        $query = AuxList::query()
            -> where('is_active', true)
            -> orderBy('aux_order');

        return $query -> get();
    }
    
    /**
     *
     * Updates the status whether the acount codes is active or inactive
     *
     * @param String $session_id
     * @param Boolean $active
     * @return Boolean
     * @throws conditon
     **/
    public function codeAssignDelete($data)
    {
        $account = $data['account'];
        $options_list_id = (int)$data['options_list_id'];
        $checked = $data['checked'];

        if($checked)
        {
            return DB::table('codes_per_account')->insert(
                [
                    'options_list_id'   => $options_list_id,
                    'account'           => $account
                ]
            );
        }
        else
            DB::table('codes_per_account')->where('options_list_id', $options_list_id)->where('account', $account)->delete();
    }

    public function auxAssignDelete($data)
    {
        $account = $data['account'];
        $options_list_id = (int)$data['options_list_id'];
        $checked = $data['checked'];

        if($checked)
        {
            $aux_per_account = new AuxPerAccount;
            $aux_per_account->aux_list_id = $options_list_id;
            $aux_per_account->account = $account;
            $aux_per_account->save();
        } else {
            AuxPerAccount::where('aux_list_id', $options_list_id)->where('account', $account)->delete();
        }
    }

    /**
     *
     * Add code
     *
     * @param String $session_id
     * @param Boolean $active
     * @return Boolean
     * @throws conditon
     **/
    public function addCode($data)
    {
        $options_list_id = (int)$data['options_list_id'];
        if($options_list_id == 0)
        {
            DB::table('options_list')->updateOrInsert(
                [
                    'options'       => $data['code'],
                    'category_id'   => (int)$data['category']
                ],
                [
                    'type'          => 'assigned'
                ]
            );
        }
        else
        {
            DB::table('options_list')->updateOrInsert(
                [
                    'id'   => (int)$data['options_list_id']
                ],
                [
                    'options'       => $data['code'],
                    'category_id'   => (int)$data['category'],
                    'type'          => 'assigned'
                ]
            );
        }

    }
    /**
     *
     * Add aux
     *
     * @param String $session_id
     * @param Boolean $active
     * @return Boolean
     * @throws conditon
     **/
    public function addAux($data)
    {
        $options_list_id = (int)$data['options_list_id'];
        $auxCapsLock = strtoupper($data['aux']);

        $check_duplicate = Auxlist::where('name', $auxCapsLock)->get()->toArray();

        if (count($check_duplicate) > 0) {
            return $check_duplicate;
        } else {
            if ($auxCapsLock == 'LOGIN' || $auxCapsLock == 'LOGOUT') {
                AuxList::updateOrCreate(
                    ['id' => $options_list_id],
                    [
                        'name' => $auxCapsLock,
                        'aux_status' => $auxCapsLock == 'LOGIN' ? 'ACTIVE' : 'INACTIVE'
                    ]
                );
            } else {
                AuxList::updateOrCreate(
                    ['id' => $options_list_id],
                    [
                        'name' => $auxCapsLock
                    ]
                );
            }
        }
    }
    /**
     *
     * Delete code by setting it inactive
     *
     * @param String $session_id
     * @param Boolean $active
     * @return Boolean
     * @throws conditon
     **/
    public function deleteCode($data)
    {
        $options_list_id = (int)$data['options_list_id'];
        DB::table('options_list')->updateOrInsert(
            [
                'id'   => (int)$data['options_list_id']
            ],
            [
                'is_active'     => 0
            ]
        );
    }

    public function deleteAux($data)
    {
        $options_list_id = (int)$data['options_list_id'];
        AuxList::where('id', $options_list_id)->delete();
    }

    /**
     *
     * Get Subnet Mapping List
     *
     **/
    public function getMappingList($page = 1, $per_page = 20, $search="")
    {
        $query = DB::table('agent_network');
        
        if($search != "")
            $query ->where('subnet', 'like', "%$search%");
            $query ->orwhere('location', 'like', "%$search%");
            $query ->orwhere('site', 'like', "%$search%");
            $query ->orwhere('client', 'like', "%$search%");
            $query ->orwhere('type', 'like', "%$search%");
        return $query -> paginate($per_page, ['*'], 'page', $search ? 1 : $page );
    }

    /**
     *
     * Get Applications List
     *
     **/
    public function getApplicationsList($page = 1, $per_page = 20, $search="")
    {
        $query = ApplicationsList::query();
        
        if($search != "") {
            $query ->where('name', 'like', "%$search%");
            $query ->orwhere('account_affected', 'like', "%$search%");
        }
        
        return $query -> paginate($per_page, ['*'], 'page', $page );
    }

    public function AddMapping($data)
    {
        $query = DB::table('agent_network') -> Insert(
            [
                'subnet'    => $data -> input('subnet'),
                'location'  => $data -> input('location'),
                'site'      => $data -> input('site'),
                'client'    => $data -> input('client'),
                'type'      => $data -> input('type')
            ]
        );
    }

    public function EditMapping($data)
    {
        DB::table('agent_network')
             -> where ('id' , $data ->input('id')) 
             -> update([
                'subnet'    => $data -> input('data')['subnet'],
                'location'  => $data -> input('data')['location'],
                'site'      => $data -> input('data')['site'],
                'client'    => $data -> input('data')['client'],
                'type'      => $data -> input('data')['type']
             ]);
    }

    public function AddEditApplication($data)
    {
        ApplicationsList::updateOrCreate(
            ['id' => $data ->input('id')],
            [
                'name' => $data -> input('data')['application'],
                'type' => $data -> input('data')['type'],
                'account_affected' => $this -> _arrayEncode( $data -> input('data')['account_affected'])
            ]
        );
    }

    /**
     *
     * Parse array params to implode version
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    private function _arrayEncode( $data ){
        if( $data )
            return implode( ",", $data);
        
        return null;
    }

    public function deleteApplication($id)
    {
        ApplicationsList::where('id', $id)->delete();
        AgentApplications::where('application_id', $id)->delete();
    }
    
    public function deleteMapping($id)
    {
        $query = DB::table('agent_network') ->where('id', $id)->delete();
    }

    public function AddVlanMapping($data)
    {
        $query = DB::table('vlan') -> Insert(
            [
                'name'      => $data -> input('name'),
                'subnet'    => $data -> input('subnet'),
                'acount'   => $data -> input('account'),
                'is_active' => $data -> input('is_active')
            ]
        );
    }


    public function getVlanMappingList($page = 1, $per_page = 20, $search="")
    {
        $query = DB::table('vlan');
        
        if($search != "")
            $query ->where('subnet', 'like', "%$search%");
            $query ->orwhere('acount', 'like', "%$search%");
            $query ->orwhere('name', 'like', "%$search%");
        return $query -> paginate($per_page, ['*'], 'page', $search ? 1 : $page );
    }

    public function EditVlanMapping($data)
    {
        DB::table('vlan')
             -> where ('id' , $data ->input('id')) 
             -> update([
                'name'          => $data -> input('data')['name'],
                'subnet'        => $data -> input('data')['subnet'],
                'acount'       => $data -> input('data')['account'],
                'is_active'     => $data -> input('data')['is_active']
             ]);
    }

    public function deleteVlanMapping($id)
    {
        $query = DB::table('vlan') ->where('id', $id)->delete();
    }

    public function getLocationList()
    {
        $query = DB::table('cnx_employees')->select('country')->distinct()->get();

        return $query;

    }

    public function getClientList()
    {
        $query = DB::table('cnx_employees')->select('msa_client')->distinct()->get();

        return $query;
    }

    public function getUserAccountList()
    {
        $query = CnxEmployees::select('msa_client')->distinct()->get();

        return $query;
    }

    public function getNetorkType( $subnet ){

        $details = DB::Table('agent_network')-> where('subnet', $subnet ) -> first();

        if( $details )
        {
            $type = $details -> type;
            $status = 'Mapped';
        }else{
            $type = $this -> _netType($subnet);
            $status = 'UnMapped';
        }
            
         return array('status' => $status, 'net_type' => $type);
    }

    private function _netType($host_ip_address)
    {
        $arr = explode(".", $host_ip_address, 2);
        $first_octet = $arr[0];
        $network_type = "WAH";
        if($first_octet == "10")
            $network_type = "WAH/VPN";
        return $network_type;
    }

    public function getDesktopApplicationList($page = 1 , $per_page = 50)
    {
        return DesktopApplicationList::paginate($per_page, ['*'], 'page', $page );
    }

    public function getDeploymentApplicationsList($page = 1 , $search = '', $per_page = 50)
    {
        return DesktopApplicationListResource::collection(DesktopApplicationList::with(['installer'])
        ->when($search, function(Builder $query, $value) {
            $query->where('name', 'like', "%$value%")
            ->orWhere('description', 'like', "%$value%");
        })->paginate($per_page, ['*'], 'page', $page ));  
    }

    public function deleteDeploymentApplication($data)
    {
        DesktopApplicationList::where('id', $data['id'])->delete();
    }

    public function getAllAgentSession(){
        $session_id = AgentConnection::select('session_id')
            ->where('is_active', true )->get();

        return $session_id;
    }

    /**
     *
     * Get Applications 
     *
     **/
    public function getApplications($page = 1, $per_page = 20, $search="")
    {
        $query = Applications::query();
        
        if($search != "") {
            $query ->where('name', 'like', "%$search%");
            $query ->orwhere('description', 'like', "%$search%");
            $query ->orwhere('account', 'like', "%$search%");
        }
        
        return $query -> paginate($per_page, ['*'], 'page', $page );
    }


    public function AddEditApplicationURL($data)
    {
        Applications::updateOrCreate(
            ['id' => $data ->input('id')],
            $data -> input()
        );

        $app = $this -> getApplicationUrlByID($data -> input('id'));
        $applicationUrls = $this -> getApplicationsUrlByAccountID($app -> account_id);
        $this -> removeAppUrlRedisData(strtolower($app -> account));
        $this -> storeAppUrlToRedis(strtolower($app -> account), $applicationUrls);
    }

    public function getApplicationUrlByID($id)
    {
        $query = Applications::query() -> where('id', $id);
        return $query -> first();
    }

    public function getApplicationsUrlByAccountID($id)
    {
        $queryAll = Applications::query();
        $queryAll -> select('url');
        $queryAll -> where('account_id', $id);

        $allApps = $queryAll -> get();
        $selectedApps  = Applications::query() -> select('url') -> where(array('account_id' => $id, 'is_loaded' => true)) -> get();

        return ['all' => $allApps, 'selected' => $selectedApps];
    }

    
    function storeAppUrlToRedis($account, $details)
    {
        Redis::hmset('ApplicationUrlList.'.$account, $details);
    }

    function getAppUrlDetailsFromRedis($account, $details = 'selected')
    {
        $key = 'ApplicationUrlList.'.$account; 
        $data = Redis::hgetall($key);
        return json_encode($data);
    }

    function getAllAppUrls()
    {
        $keys = Redis::keys('ApplicationUrlList.*');
        $clients = [];
        if(!empty($keys))
        {
            foreach ($keys as $key) {
                $stored = Redis::hgetall($key);
                $clients[] = $stored;
            }
        }
        return $clients;
    }

    function removeAppUrlRedisData($id)
    {
        return Redis::del('ApplicationUrlList.'.$id);

    }

    function initializeApplicationURLsInRedis()
    {
        $apps = Applications::query() -> get();
        
        return $apps;
    }

    public function getSoftwareUpdates($page = 1, $per_page = 20, $search="") {
        $query = UserSoftwareUpdate::select('os', 'patch_name', 'update_id')->groupBy('os', 'patch_name', 'update_id');
        
        if($search != "") {
            $query ->where('os', 'like', "%$search%");
            $query ->orwhere('patch_name', 'like', "%$search%");
        }
        
        return $query -> paginate($per_page, ['*'], 'page', $page );
    }

    public function executeSoftwareUpdate($update_id, $patch_name) {
        $query = UserSoftwareUpdate::from('users_software_updates as usu')
                ->select('usu.worker_id', 'ac.session_id')
                ->leftJoin('agent_connections as ac', 'ac.worker_id', '=', 'usu.worker_id' )
                ->where('ac.is_active', true)
                ->where('usu.is_installed', false)
                ->where('patch_name', $patch_name)
                ->where('update_id', $update_id)
                ->groupBy('worker_id', 'ac.session_id')
                ->get();
        
        return $query;
    }

    public function saveSoftwareUpdates($worker_id, $os, $data) {
        DB::beginTransaction();

        UserSoftwareUpdate::where('worker_id', $worker_id)->delete();

        foreach($data as $value) {
            
            UserSoftwareUpdate::create(
                [
                    'worker_id'=> $worker_id,
                    'os'=> $os,
                    'patch_name' => $value['title'],
                    'update_id' => $value['updateId'], 
                    'description' => $value['description'],
                    'is_installed' => $value['is_installed'],
                    'support_url' => $value['support_url']   
                ]
            );
        }

        DB::commit();
    }

    public function softwareUpdateResult($data) {
        LogsSoftwareUpdate::create(
            [
                'worker_id'=> $data['workerId'],
                'patch_name' => $data['title'],
                'update_id' => $data['updateId'], 
                'description' => $data['description'],
                'is_installed' => $data['is_installed']
            ]
        );
    }
    
    public function getApplicationsInstaller() 
    {
        $new_data_array = array();
        $current_date = Carbon::now()->format('Y-m-d');
       
        $data = DesktopApplicationList::query()
            ->addSelect('*')
            ->addSelect('desktop_application_list.id as desktop_id', 'installers.id as installer_id')
            ->leftJoin('installers', 'installers.application_id', '=', 'desktop_application_list.id')
            ->where('execution_date', '=', $current_date)
            ->get()->toArray();

        foreach($data as $value) {
            $value['download_path'] = Storage::disk($value['disk'])->url($value['directory'].'/'.$value['base_filename'].'.'.$value['extension']);
            array_push($new_data_array, $value);
        }
        
        return $new_data_array;
    }

    public function getAgentSession( $worker_id = 0 ){
        $connection = AgentConnection::where('worker_id', $worker_id ) 
           ->first();

        return $connection->session_id;
    }
    
    function countUrl($url, $account)
    {
        $counter = Applications::where(array('account' => $account, 'url' => $url))
                                ->increment('url_counter', 1);
        return $counter;
    }

    function getTopUrls($account)
    {
        $urls = Applications::where('account', $account)
                            ->where('url_counter', 1)
                            ->orderBy('url_counter','DESC')->limit(10)->get();
        return $urls;
    }

    function resetUrlCounter()
    {
        return Applications::query()->update(array('url_counter' => 0));
    }
        
    public function getMailNotifications($page = 1, $per_page = 20, $search="", $email = "") {
        $query = SmartReportType::select('smart_report_types.*');
        $query->addSelect(DB::raw("(
            SELECT 
            CASE
                WHEN rtpu.report_type_id IS NULL THEN 'Enable'
                ELSE 'Disable'
            END
            FROM `report_types_per_user` rtpu WHERE rtpu.report_type_id = smart_report_types.id AND rtpu.email = '$email'
        ) AS status_code"));
        $query->addSelect(DB::raw("'$email' as email"));
        $query->orderBy('smart_report_types.report_type');
      
        if($search != "") {
            $query->where('smart_report_types.report_type', 'like', "%$search%");
        }
        return $query->paginate($per_page, ['*'], 'page', $search ? 1 : $page );
    }

    public function updateMailNotifications($data) {

        if(!$data['data']['status_code']) {
            ReportTypePerUser::create([
                'report_type_id' => $data['data']['id'],
                'email' => $data['data']['email']
            ]);
        } else {
            ReportTypePerUser::where('email', $data['data']['email'])
                ->where('report_type_id', $data['data']['id'])->delete();
        }
    }

    public function getTimeFrames($accounts, $page = 1, $per_page = 20, $search="") {
        $query = TimeFrame::select('*');
        $query->orderByDesc('id');
      
        if(count($accounts) > 0) {
            $query->whereIn('account', $accounts);
        }
        
        if($search != "") {
            $query->where('account', 'like', "%$search%");
        }
        return $query->paginate($per_page, ['*'], 'page', $search ? 1 : $page );
    }

    public function addEditTimeFrame($data)
    {
        $start_date = date('Y-m-d H:i:s', strtotime($data -> input('data')['start_date']));
        $end_date = date('Y-m-d H:i:s', strtotime($data -> input('data')['end_date']));

        TimeFrame::updateOrCreate(
            ['id' => $data ->input('id')],
            [
                'account' => $data -> input('data')['account'],
                'data_to_track' => $this -> _arrayEncode( $data -> input('data')['data_to_track']),
                'start_date' => $start_date,
                'end_date' => $end_date
            ]
        );
    }

    public function deleteTimeFrame($id)
    {
        TimeFrame::where('id', $id)->delete();
    }

    public function isOverlappingTimeFrame($data)
    {
        $account = $data -> input('data')['account'];
        $start_date = date('Y-m-d H:i:s', strtotime($data -> input('data')['start_date']));
        $end_date = date('Y-m-d H:i:s', strtotime($data -> input('data')['end_date']));
        $query = TimeFrame::where('account', 'like', "%$account%");
        
        if(isset($data['id'])) {
            $id = $data['id'];
            $query->where('id', '<>', $id);
        }

        $count = $query->where(DB::raw("
            ('$start_date' between start_date and end_date or '$end_date' between start_date and end_date)
            or ('$start_date' < start_date and '$end_date' > end_date)
        "))->get()->count();

        if($count > 0) 
            return true;
        return false;
    }

    public function checkTimeFrame($account)
    {
        $data = [
            "ping" => false,
            "trace" => false,
            "mtr" => false,
            "speedtest" => false,
            "mos" => false
        ];

        $date_now = Carbon::now()->format('Y-m-d H:i:s');
        $timeframe = TimeFrame::where('account', 'like', "%$account%")
                            ->where('start_date', '<', "$date_now")
                            ->where('end_date', '>', "$date_now")
                            ->first();
        if(!isset($timeframe)) return $data;
        $data_to_track_arr = explode(",", $timeframe["data_to_track"]);
        foreach($data_to_track_arr as $val) {
            if(array_key_exists($val, $data)) $data[$val] = true;
        }       

        return $data;
    }

    // /**
    //  * Retrieve the list of account access of the current user
    //  *
    //  * @return type
    //  * @throws conditon
    //  **/
    // public function getCurrentUserAccountAccess()
    // {
    //     $account_access = DB::table('users')->select('account_access')->where();
    //     // return $results = DB::table('accounts')-> where( 'is_active', 1 )->orderBy('name')->get();
    // }

    public function saveAndUpdateAgentTheme($details)
    {
        $detailObject = (object) $details;
        $agentTheme= AgentTheme::where(['worker_id' => $detailObject->worker_id]) -> count();
        if($agentTheme > 0)
        {
            return AgentTheme::where(array('worker_id' => $detailObject->worker_id)) -> update($details);
        }else{
            return AgentTheme::insert($details);
        }


    }

    public function getAgentTheme($worker_id)
    {
        return AgentTheme::where('worker_id', $worker_id)->get();
    }
}
