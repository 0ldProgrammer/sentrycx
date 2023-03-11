<?php 

namespace App\Modules\Reports\Services;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

use App\Modules\Reports\Models\ReportTypePerUser;
use App\Modules\Reports\Models\User;
use App\Modules\Reports\Models\LogSpeedtest;
use App\Modules\Reports\Models\SmartReportType;
use App\Modules\Reports\Models\CnxEmployee;
use App\Modules\Reports\Models\AgentApplication;
use App\Modules\Reports\Models\MetricThreshold;
use Illuminate\Support\Facades\Mail;
use App\Mail\AutoSmartReportsMail;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\App;
use Log;

use App\Modules\WorkstationModule\Models\AgentConnection;
use App\Modules\WorkstationModule\Models\AgentApplications;
use App\Modules\WorkstationModule\Models\ApplicationsList;
use App\Modules\Maintenance\Models\CnxEmployees;
use App\Modules\Reports\Models\ReportType;
use App\Modules\Reports\Models\Threshold;
use App\Modules\Reports\Models\Account;
use Carbon\Carbon;



class ReportService {
    protected $urlGenerator;

    protected $DB_READ;

    public function __construct()
    {
        $this->urlGenerator = app(\App\Services\UrlGenerator::class);

        $this->DB_READ = DB::connection(config('dbreadwrite.db_read'));
    }
    /**
     *
     * Export to excel
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/

    const REPORT_TYPE_REF = [
        'mos' => 'logs_mos',
        'speedtest' => 'logs_speedtest',
        'mtr' => 'monitoring',
        'ping' => 'monitoring'
    ];
    
    const DEFAULT_THRESHOLDS = [
        ['report_id' => 2, 'name' => 'Upload Speed', 'threshold' => 5],
        ['report_id' => 2, 'name' => 'Download Speed', 'threshold' => 10],
        ['report_id' => 4, 'name' => 'Ping', 'threshold' => 200],
        ['report_id' => 7, 'name' => 'CPU', 'threshold' => 26],
        ['report_id' => 7, 'name' => 'Disk', 'threshold' => 50],
        ['report_id' => 7, 'name' => 'RAM', 'threshold' => 12],
    ];


    public function querySpeedtest($params)
    {
        // SELECT 
        // ce.employee_number,
        // ce.firstname,
        // ce.lastname ,
        // ce.msa_client,
        // ls.download_speed,
        // ls.upload_speed,
        // ls.updated_at 
        // FROM logs_speedtest ls
        // JOIN cnx_employees ce ON ce.employee_number = CONCAT(ls.worker_id, '')
        // LEFT JOIN thresholds t ON t.account_id = ce.msa_client  
        // WHERE (ls.download_speed != '' OR ls.upload_speed != '')  
        // AND ls.updated_at BETWEEN '2021-01-05' AND '2022-08-03'
        // -- IF (USER SELECTED SPECIFIC ACCOUNT)
        // AND ce.msa_client = 'BELKIN'
        // -- IF (USER SELECTED SPECIFIC WORKER)
        // AND ce.employee_number = '101576517'
        // -- IF (USER SELECTED BELOW THRESHOLD)
        // AND CAST(REGEXP_REPLACE(ls.download_speed,'[^0-9.]','')+0 AS DECIMAL(8,2)) < t.download_speed
        // AND CAST(REGEXP_REPLACE(ls.upload_speed,'[^0-9.]','')+0 AS DECIMAL(8,2)) < t.upload_speed
        // $where = "ls.created_at >= '2021-01-10' AND ls.created_at <= '2022-07-29' AND mt.name = 'Download Speed' ";

        $query = $this->DB_READ->table('agent_connections as ac');
        $query -> select([
            'ls.updated_at as Date', 'agent_name as Agent Name','agent_email as Email','ac.worker_id as Employee ID','account as Account', 'ac.connection_type as Connection Type',
            'ac.location as Location', 'ac.country as Country','ls.download_speed as Download Speed', 
            'ls.upload_speed as Upload Speed'
        ]);

        $query->leftJoin('workstation_profile AS wp', function( $join ){
            $join->on( 'wp.worker_id', '=', 'ac.worker_id')
            ->on('wp.id', '=', DB::raw("(SELECT max(id) from workstation_profile WHERE workstation_profile.worker_id = ac.worker_id AND redflag_id=0)"));
        });

        $query -> leftJoin('logs_speedtest as ls', function($join){
            $join -> on('ac.worker_id','=','ls.worker_id');
        });

        $query -> leftJoin('accounts as a', function($join){
            $join -> on('ac.account','=','a.name');
        });


        // $query -> whereNotIn('ls.download_speed',[]);
        // $query -> whereNotIn('ls.upload_speed',[]);
        if (isset($params->startDate) && $params->startDate &&
            isset($params->endDate) && $params->endDate){
                $start_date = date('Y-m-d', strtotime($params->startDate));
                $start_date = new Carbon($start_date);
                $start_date = $start_date -> addDays(1);
                $end_date = date('Y-m-d', strtotime($params->endDate));
                $end_date = new Carbon($end_date);
                $end_date = $end_date -> addDays(2);
            $query -> whereBetween('ls.updated_at', [$start_date, $end_date]);
        }

        if(isset($params->locationSelect) && $params->locationSelect){
            $query -> whereIn('ac.location', $params -> locationSelect);
        }
        if(isset($params->selectedAgent) && $params->selectedAgent){
            $query -> where('wp.net_type', $params -> selectedAgent);
        }
        if(isset($params->selectedConnection) && $params->selectedConnection){
            $query -> where('wp.connection_type', $params -> selectedConnection);
        }
        
        if(isset($params->selectedVLAN) && $params->selectedVLAN){
            $query -> where('wp.VLAN', $params -> selectedVLAN);
        }

        if(isset($params->selectedAccount) && $params->selectedAccount){
            $query -> whereIn('ac.account', $params->selectedAccount);

            switch(true)
            {
                case count($params->selectedAccount) == 1:
                    if(isset($params -> selectedOption) && $params -> selectedOption)
                    {
                        $query -> leftJoin('metrics_thresholds as mt', function($join){
                            $join -> on('a.id','=','mt.account_id');
                        });

                        $threshold = $this -> getThresholdByAccount(2, $params->selectedAccount[0]);

                        $query -> whereRaw('ls.download_speed '.$params -> selectedOption.' '.$threshold[0] -> threshold);
                        $query -> whereRaw('ls.upload_speed '.$params -> selectedOption.' '.$threshold[1] -> threshold);
                    }

                break;
                case count($params->selectedAccount) > 1:

                    if(isset($params -> selectedOption) && $params -> selectedOption)
                    {
                        $query -> leftJoin('metrics_thresholds as mt', function($join){
                            $join -> on('a.id','=','mt.account_id');
                        });
                        $query -> where( function( $q ) use ($params) {
        
                            $q -> where(function ($que) use ($params) {

                                $threshArray = count($params -> selectedThreshold);

                                
                                $threshold = $this -> getThresholdByAccount(2, $params->selectedAccount[0]);
                                // print_r($threshold);

                                if($threshArray > 0)
                                {
                                    if($threshold[0]->mt_id == $params -> selectedThreshold[0] || $threshold[1]->mt_id == $params -> selectedThreshold[0])
                                    {
                                        $threshold = $this -> getThresholdByID($params -> selectedThreshold[0]);

                                        $option = str_replace(' ','_',strtolower($threshold->mt_name));
                                        // echo $params -> selectedOption;
                        
                                        $que -> whereRaw('ls.'.$option.' '.$params -> selectedOption.' '.$threshold -> threshold);
                                    }else{
                                        $que -> whereRaw('ls.download_speed '.$params -> selectedOption.' '.$threshold[0] -> threshold);
                                        $que -> whereRaw('ls.upload_speed '.$params -> selectedOption.' '.$threshold[1] -> threshold);
                                    }

                                }else{
                                    $que -> whereRaw('ls.download_speed '.$params -> selectedOption.' '.$threshold[0] -> threshold);
                                    $que -> whereRaw('ls.upload_speed '.$params -> selectedOption.' '.$threshold[1] -> threshold);
                                }
                
                            });
                            
                            foreach($params->selectedAccount as $sa)
                            {
                                $q -> orWhere(function ($que) use ($params, $sa) {

                                    $threshArray = count($params -> selectedThreshold);
                                    $threshold = $this -> getThresholdByAccount(2,$sa);
                        
                                    if($threshArray > 0)
                                    {
                                        if($threshold[0]->mt_id == $params -> selectedThreshold[0] || $threshold[1]->mt_id == $params -> selectedThreshold[0])
                                        {
                                            $threshold = $this -> getThresholdByID($params -> selectedThreshold[0]);

                                            $option = str_replace(' ','_',strtolower($threshold->mt_name));
                                            // echo $params -> selectedOption;
                            
                                            $que -> whereRaw('ls.'.$option.' '.$params -> selectedOption.' '.$threshold -> threshold);
                                        }else{
                                            $que -> whereRaw('ls.download_speed '.$params -> selectedOption.' '.$threshold[0] -> threshold);
                                            $que -> whereRaw('ls.upload_speed '.$params -> selectedOption.' '.$threshold[1] -> threshold);
                                        }
                                    }else{

                                        $que -> whereRaw('ls.download_speed '.$params -> selectedOption.' '.$threshold[0] -> threshold);
                                        $que -> whereRaw('ls.upload_speed '.$params -> selectedOption.' '.$threshold[1] -> threshold);
                                    }
                                });

                            }
                
                        });
                    }
                break;
            }
        }elseif(isset($params -> selectedOption) && $params -> selectedOption)
        {   
            $query -> leftJoin('metrics_thresholds as mt', function($join){
                $join -> on('a.id','=','mt.account_id');
            });
            $threshold = $this -> getThresholdByAccount(2,0);
            // print_r($threshold);
            $query -> whereRaw('ls.download_speed '.$params -> selectedOption.' '.$threshold[0] -> threshold);
            $query -> whereRaw('ls.upload_speed '.$params -> selectedOption.' '.$threshold[1] -> threshold);
        }

        
        $final_data = json_encode(array(
            'data'  => $query->get(),
            'count' => $query->count()
        ));

        return $final_data;

    }

    public function queryOfflineWorkstation($params)
    {
        // SELECT 
        //     ac.worker_id ,
        //     ac.agent_name,
        //     ac.agent_email ,
        //     ac.station_name,
        //     ac.location,
        //     ac.country,
        //     ac.last_logged_in 
        // FROM agent_connections ac 
        // JOIN (
        //         SELECT max(id), worker_id 
        //         FROM workstation_profile
        //         GROUP BY worker_id
        //     ) wp ON wp.worker_id = ac.worker_id 
        // WHERE ac.is_active = 0
        // AND ac.last_logged_in BETWEEN '2022-01-05' AND '2022-08-03'
        // AND ac.account = 'BELKIN'
        // AND ac.worker_id = '793768'
        $query = $this->DB_READ->table('agent_connections as ac');

        $query -> select([
            'ac.worker_id as Employee ID', 'ac.agent_name as Agent Name', 'ac.agent_email as Email', 'ac.account as Account',
            'ac.station_name as Workstation ID', 'ac.location as Location', 'ac.country as Country', DB::raw('DATEDIFF(NOW(),last_logged_in) as `LAST ONLINE`')
        ]);
        $query->leftJoin('workstation_profile AS wp', function( $join ){
            $join->on( 'wp.worker_id', '=', 'ac.worker_id')
            ->on('wp.id', '=', DB::raw("(SELECT max(id) from workstation_profile WHERE workstation_profile.worker_id = ac.worker_id AND redflag_id=0)"));
        });
        $query -> where('ac.is_active', false);
        if (isset($params->startDate) && $params->startDate &&
            isset($params->endDate) && $params->endDate){
                $start_date = date('Y-m-d', strtotime($params->startDate));
                $start_date = new Carbon($start_date);
                $start_date = $start_date -> addDays(1);
                $end_date = date('Y-m-d', strtotime($params->endDate));
                $end_date = new Carbon($end_date);
                $end_date = $end_date -> addDays(2);
            $query -> whereBetween('ac.last_logged_in', [$start_date, $end_date]);
        }

        if(isset($params->employee_id) && $params->employee_id){
            $query -> whereIn('ac.worker_id', $params -> employee_id);
        }
        if(isset($params->selectedAccount) && $params->selectedAccount){
            $query -> whereIn('ac.account', $params -> selectedAccount);
        }
        if(isset($params->locationSelect) && $params->locationSelect){
            $query -> whereIn('ac.location', $params -> locationSelect);
        }
        if(isset($params->selectedAgent) && $params->selectedAgent){
            $query -> where('wp.net_type', $params -> selectedAgent);
        }

        if(isset($params->selectedVLAN) && $params->selectedVLAN){
            $query -> where('wp.VLAN', $params -> selectedVLAN);
        }
        
        if(isset($params->selectedConnection) && $params->selectedConnection){
            $query -> where('wp.connection_type', $params -> selectedConnection);
        }

        $final_data = json_encode(array(
            'data'  => $query->get(),
            'count' => $query->count()
        ));

        return $final_data;
    }

    public function queryMTR($params)
    {
        // SELECT
        // lwp.created_at AS 'Date Created',
        // agent_name AS 'Agent Name',
        // agent_email AS 'Agent Email',
        // wp.`station_number` AS 'Workstation',
        // location AS 'Location',
        // ac.worker_id AS 'Employee ID',
        // lwp.mtr_highest_avg AS 'MTR Highest Avg',
        // lwp.mtr_highest_loss AS 'MTR Highest Loss',
        // lwp.mtr_host  AS 'MTR Host'
        // FROM agent_connections AS `ac` LEFT JOIN `workstation_profile` AS `wp` ON `wp`.`worker_id` = `ac`.`worker_id`
        // AND `wp`.`id` = (SELECT MAX(id) FROM workstation_profile WHERE workstation_profile.worker_id = ac.worker_id AND redflag_id=0)
        // LEFT JOIN logs_workstation_profile AS lwp ON lwp.worker_id = ac.`worker_id`
        // LEFT JOIN accounts ON accounts.name = ac.`account`
        // LEFT JOIN metrics_thresholds AS mt ON mt.`account_id` = accounts.id
        // WHERE lwp.created_at >= '2021-01-10' AND lwp.created_at <= '2022-08-21'
        // AND mt.name = 'MTR Highest Avg'
        // AND (
        // (mt.account_id = 288 AND lwp.mtr_highest_avg >= 50 AND mt.id = 27)
        // );
        
        $query = $this->DB_READ->table('agent_connections as ac');
        $query -> select([
            'lwp.updated_at as Date Updated', 'ac.worker_id as Worker ID', 'ac.agent_name as Name', 'ac.agent_email as Email',
            'ac.account as Account', 'ac.station_name as Workstation', 'location as Location', 'ac.worker_id as Employee ID',
            'lwp.mtr_highest_avg as MTR Highest AVG','lwp.mtr_highest_loss as MTR Highest Loss', 'lwp.mtr_host as MTR Host'
        ]);

        $query->leftJoin('workstation_profile AS wp', function( $join ){
            $join->on( 'wp.worker_id', '=', 'ac.worker_id')
            ->on('wp.id', '=', DB::raw("(SELECT max(id) from workstation_profile WHERE workstation_profile.worker_id = ac.worker_id AND redflag_id=0)"));
        });

        $query -> leftJoin('logs_workstation_profile as lwp', function($join){
            $join -> on('lwp.worker_id','=','ac.worker_id');
        });

        $query -> leftJoin('accounts as a', function($join){
            $join -> on('a.name','=','ac.account');
        });

        if (isset($params->selectedAccount) && $params->selectedAccount){
            $query -> whereIn('ac.account', $params->selectedAccount);
        }

        if(isset($params->employee_id) && $params->employee_id){
            $query -> whereIn('ac.worker_id', $params -> employee_id);
        }

        if(isset($params->locationSelect) && $params->locationSelect){
            $query -> whereIn('ac.location', $params -> locationSelect);
        }

        if(isset($params->selectedConnection) && $params->selectedConnection){
            $query -> where('ac.connection_type', $params -> selectedConnection);
        }

        if(isset($params->selectedVLAN) && $params->selectedVLAN){
            $query -> where('lwp.VLAN', $params -> selectedVLAN);
        }

        if (isset($params->startDate) && $params->startDate &&
            isset($params->endDate) && $params->endDate){
                $start_date = date('Y-m-d', strtotime($params->startDate));
                $start_date = new Carbon($start_date);
                $start_date = $start_date -> addDays(1);
                $end_date = date('Y-m-d', strtotime($params->endDate));
                $end_date = new Carbon($end_date);
                $end_date = $end_date -> addDays(2);
            $query -> whereBetween('lwp.updated_at', [$start_date, $end_date]);

        }    

        
        if(isset($params->selectedAccount) && $params->selectedAccount){
            $query -> whereIn('ac.account', $params->selectedAccount);

            switch(true)
            {
                case count($params->selectedAccount) == 1:
                    if(isset($params -> selectedOption) && $params -> selectedOption)
                    {
                        $threshold = $this -> getThresholdByAccount(3, $params->selectedAccount[0]);

                        $query -> whereRaw('lwp.mtr_highest_avg '.$params -> selectedOption.' '.$threshold[0] -> threshold);
                        $query -> whereRaw('lwp.mtr_highest_loss '.$params -> selectedOption.' '.$threshold[1] -> threshold);
                    }

                break;
                case count($params->selectedAccount) > 1:

                    if(isset($params -> selectedOption) && $params -> selectedOption)
                    {
                        $query -> where( function( $q ) use ($params) {
        
                            $q -> where(function ($que) use ($params) {

                                $threshArray = count($params -> selectedThreshold);

                                
                                $threshold = $this -> getThresholdByAccount(3, $params->selectedAccount[0]);
                                // print_r($threshold);

                                if($threshArray > 0)
                                {
                                    if($threshold[0]->mt_id == $params -> selectedThreshold[0] || $threshold[1]->mt_id == $params -> selectedThreshold[0])
                                    {
                                        $threshold = $this -> getThresholdByID($params -> selectedThreshold[0]);

                                        $option = str_replace(' ','_',strtolower($threshold->name));
                                        // echo $params -> selectedOption;
                        
                                        $que -> whereRaw('lwp.'.$option.' '.$params -> selectedOption.' '.$threshold -> threshold);
                                    }else{
                                        $que -> whereRaw('lwp.mtr_highest_avg '.$params -> selectedOption.' '.$threshold[0] -> threshold);
                                        $que -> whereRaw('lwp.mtr_highest_loss '.$params -> selectedOption.' '.$threshold[1] -> threshold);
                                    }

                                }else{
                                    $que -> whereRaw('lwp.mtr_highest_avg '.$params -> selectedOption.' '.$threshold[0] -> threshold);
                                    $que -> whereRaw('lwp.mtr_highest_loss '.$params -> selectedOption.' '.$threshold[1] -> threshold);
                                }
                
                            });
                            
                            foreach($params->selectedAccount as $sa)
                            {
                                $q -> orWhere(function ($que) use ($params, $sa) {

                                    $threshArray = count($params -> selectedThreshold);
                                    $threshold = $this -> getThresholdByAccount(3,$sa);
                        
                                    if($threshArray > 0)
                                    {
                                        if($threshold[0]->mt_id == $params -> selectedThreshold[0] || $threshold[1]->mt_id == $params -> selectedThreshold[0])
                                        {
                                            $threshold = $this -> getThresholdByID($params -> selectedThreshold[0]);

                                            $option = str_replace(' ','_',strtolower($threshold->name));
                                            // echo $params -> selectedOption;
                            
                                            $que -> whereRaw('lwp.'.$option.' '.$params -> selectedOption.' '.$threshold -> threshold);
                                        }else{
                                            $que -> whereRaw('lwp.mtr_highest_avg '.$params -> selectedOption.' '.$threshold[0] -> threshold);
                                            $que -> whereRaw('lwp.mtr_highest_loss '.$params -> selectedOption.' '.$threshold[1] -> threshold);
                                        }
                                    }else{

                                        $que -> whereRaw('lwp.mtr_highest_avg '.$params -> selectedOption.' '.$threshold[0] -> threshold);
                                        $que -> whereRaw('lwp.mtr_highest_loss '.$params -> selectedOption.' '.$threshold[1] -> threshold);
                                    }
                                });

                            }
                
                        });
                    }
                break;
            }
        }elseif(isset($params -> selectedOption) && $params -> selectedOption)
        {   

            $threshArray = count($params -> selectedThreshold);
            $threshold = $this -> getThresholdByAccount(3,0);

            // print_r($threshold[0]);

            if($threshArray > 0)
            {
                if($threshold[0]->mt_id == $params -> selectedThreshold[0] || $threshold[1]->mt_id == $params -> selectedThreshold[0])
                {
                    $threshold = $this -> getThresholdByID($params -> selectedThreshold[0]);

                    // print_r($threshold);

                    $option = str_replace(' ','_',strtolower($threshold->name));
                    // echo $params -> selectedOption;
    
                    $query -> whereRaw('lwp.'.$option.' '.$params -> selectedOption.' '.$threshold -> threshold);
                }else{
                    $query -> whereRaw('lwp.mtr_highest_avg '.$params -> selectedOption.' '.$threshold[0] -> threshold);
                    $query -> whereRaw('lwp.mtr_highest_loss '.$params -> selectedOption.' '.$threshold[1] -> threshold);
                }

            }else{
                $query -> whereRaw('lwp.mtr_highest_avg '.$params -> selectedOption.' '.$threshold[0] -> threshold);
                $query -> whereRaw('lwp.mtr_highest_loss '.$params -> selectedOption.' '.$threshold[1] -> threshold);
            }
        }

        
        $final_data = json_encode(array(
            'data'  => $query->get(),
            'count' => $query->count()
        ));

        return $final_data;

    }


    public function queryUtilizationsFreeDisk($params)
    {
        
        $query = $this->DB_READ->table('agent_connections as ac');
        $query -> select([
            'lwp.updated_at as Date Updated', 'ac.worker_id as Worker ID', 'ac.agent_name as Name', 'ac.agent_email as Email',
            'ac.account as Account', 'ac.station_name as Workstation','lwp.cpu as CPU','lwp.cpu_util as CPU Utilization',
            'lwp.ram as RAM','lwp.ram_usage as RAM Usage','lwp.DISK as DISK','lwp.free_disk as Free Disk'
        ]);

        $query -> leftJoin('logs_workstation_profile as lwp', function($join){
            $join -> on('lwp.worker_id','=','ac.worker_id');
        });

        $query -> leftJoin('accounts as a', function($join){
            $join -> on('a.name','=','ac.account');
        });

        if (isset($params->selectedAccount) && $params->selectedAccount){
            $query -> whereIn('ac.account', $params->selectedAccount);
        }

        if(isset($params->employee_id) && $params->employee_id){
            $query -> whereIn('ac.worker_id', $params -> employee_id);
        }

        if(isset($params->locationSelect) && $params->locationSelect){
            $query -> whereIn('ac.location', $params -> locationSelect);
        }

        if(isset($params->selectedConnection) && $params->selectedConnection){
            $query -> where('ac.connection_type', $params -> selectedConnection);
        }

        if(isset($params->selectedVLAN) && $params->selectedVLAN){
            $query -> where('lwp.VLAN', $params -> selectedVLAN);
        }

        if (isset($params->startDate) && $params->startDate &&
            isset($params->endDate) && $params->endDate){
                $start_date = date('Y-m-d', strtotime($params->startDate));
                $start_date = new Carbon($start_date);
                $start_date = $start_date -> addDays(1);
                $end_date = date('Y-m-d', strtotime($params->endDate));
                $end_date = new Carbon($end_date);
                $end_date = $end_date -> addDays(2);
            $query -> whereBetween('lwp.updated_at', [$start_date, $end_date]);

        }

        if (isset($params->selectedAccount) && $params->selectedAccount){
            switch(true)
            {
                case count($params->selectedAccount) == 1:
                    if(isset($params -> selectedOption) && $params -> selectedOption)
                    {
                        $threshold = $this -> getThresholdByAccount(8,$params->selectedAccount[0]);
                        $query -> whereRaw('lwp.free_disk '.$params -> selectedOption.' '.$threshold -> threshold);
                    }

                break;
                case count($params->selectedAccount) > 1:
                    $query -> where( function( $q ) use ($params) {
    
                        $q -> where(function ($que) use ($params) {


                            $threshold = $this -> getThresholdByAccount(8, $params->selectedAccount[0]);

                            $que -> whereRaw('lwp.free_disk '.$params -> selectedOption.' '.$threshold -> threshold);
            
                        });
                        
                        foreach($params->selectedAccount as $sa)
                        {
                            $q -> orWhere(function ($que) use ($params, $sa) {

                                $threshold = $this -> getThresholdByAccount(8,$sa);
                
                                $que -> whereRaw('lwp.free_disk '.$params -> selectedOption.' '.$threshold -> threshold);
                
                            });

                        }
            
                    });
                break;
            }
        }elseif(isset($params -> selectedOption) && $params -> selectedOption)
        {   
            $threshold = $this -> getThresholdByAccount(7,0);
            // print_r($threshold);
            $query -> whereRaw('lwp.free_disk'.$params -> selectedOption.' '.$threshold -> threshold);
        }

        $query -> orderBy('lwp.updated_at','DESC');


        $final_data = json_encode(array(
            'data'  => $query->get(),
            'count' => $query->count()
        ));

        return $final_data;

    }

    public function queryUtilizationsDISK($params)
    {
        
        $query = $this->DB_READ->table('agent_connections as ac');
        $query -> select([
            'lwp.updated_at as Date Updated', 'ac.worker_id as Worker ID', 'ac.agent_name as Name', 'ac.agent_email as Email',
            'ac.account as Account', 'ac.station_name as Workstation','lwp.cpu as CPU','lwp.cpu_util as CPU Utilization',
            'lwp.ram as RAM','lwp.ram_usage as RAM Usage','lwp.free_disk as Free Disk','lwp.DISK as DISK'
        ]);

        $query -> leftJoin('logs_workstation_profile as lwp', function($join){
            $join -> on('lwp.worker_id','=','ac.worker_id');
        });

        $query -> leftJoin('accounts as a', function($join){
            $join -> on('a.name','=','ac.account');
        });

        if (isset($params->selectedAccount) && $params->selectedAccount){
            $query -> whereIn('ac.account', $params->selectedAccount);
        }

        if(isset($params->employee_id) && $params->employee_id){
            $query -> whereIn('ac.worker_id', $params -> employee_id);
        }

        if(isset($params->locationSelect) && $params->locationSelect){
            $query -> whereIn('ac.location', $params -> locationSelect);
        }

        if(isset($params->selectedConnection) && $params->selectedConnection){
            $query -> where('ac.connection_type', $params -> selectedConnection);
        }

        if(isset($params->selectedVLAN) && $params->selectedVLAN){
            $query -> where('lwp.VLAN', $params -> selectedVLAN);
        }

        if (isset($params->startDate) && $params->startDate &&
            isset($params->endDate) && $params->endDate){
                $start_date = date('Y-m-d', strtotime($params->startDate));
                $start_date = new Carbon($start_date);
                $start_date = $start_date -> addDays(1);
                $end_date = date('Y-m-d', strtotime($params->endDate));
                $end_date = new Carbon($end_date);
                $end_date = $end_date -> addDays(2);
            $query -> whereBetween('lwp.updated_at', [$start_date, $end_date]);

        }

        if (isset($params->selectedAccount) && $params->selectedAccount){
            switch(true)
            {
                case count($params->selectedAccount) == 1:
                    if(isset($params -> selectedOption) && $params -> selectedOption)
                    {
                        $threshold = $this -> getThresholdByAccount(8,$params->selectedAccount[0]);
                        $query -> whereRaw('lwp.DISK '.$params -> selectedOption.' '.$threshold -> threshold);
                    }

                break;
                case count($params->selectedAccount) > 1:
                    $query -> where( function( $q ) use ($params) {
    
                        $q -> where(function ($que) use ($params) {


                            $threshold = $this -> getThresholdByAccount(8, $params->selectedAccount[0]);

                            $que -> whereRaw('lwp.DISK '.$params -> selectedOption.' '.$threshold -> threshold);
            
                        });
                        
                        foreach($params->selectedAccount as $sa)
                        {
                            $q -> orWhere(function ($que) use ($params, $sa) {

                                $threshold = $this -> getThresholdByAccount(8,$sa);
                
                                $que -> whereRaw('lwp.DISK '.$params -> selectedOption.' '.$threshold -> threshold);
                
                            });

                        }
            
                    });
                break;
            }
        }elseif(isset($params -> selectedOption) && $params -> selectedOption)
        {   
            $threshold = $this -> getThresholdByAccount(7,0);
            // print_r($threshold);
            $query -> whereRaw('lwp.DISK'.$params -> selectedOption.' '.$threshold -> threshold);
        }

        $query -> orderBy('lwp.updated_at','DESC');


        $final_data = json_encode(array(
            'data'  => $query->get(),
            'count' => $query->count()
        ));

        return $final_data;

    }

    public function queryUtilizationsRAMUsage($params)
    {
        
        $query = $this->DB_READ->table('agent_connections as ac');
        $query -> select([
            'lwp.updated_at as Date Updated', 'ac.worker_id as Worker ID', 'ac.agent_name as Name', 'ac.agent_email as Email',
            'ac.account as Account', 'ac.station_name as Workstation','lwp.cpu as CPU','lwp.DISK as DISK',
            'lwp.free_disk as Free Disk','lwp.cpu_util as CPU Utilization','lwp.ram as RAM','lwp.ram_usage as RAM Usage'
        ]);

        $query -> leftJoin('logs_workstation_profile as lwp', function($join){
            $join -> on('lwp.worker_id','=','ac.worker_id');
        });

        $query -> leftJoin('accounts as a', function($join){
            $join -> on('a.name','=','ac.account');
        });

        if (isset($params->selectedAccount) && $params->selectedAccount){
            $query -> whereIn('ac.account', $params->selectedAccount);
        }

        if(isset($params->employee_id) && $params->employee_id){
            $query -> whereIn('ac.worker_id', $params -> employee_id);
        }

        if(isset($params->locationSelect) && $params->locationSelect){
            $query -> whereIn('ac.location', $params -> locationSelect);
        }

        if(isset($params->selectedConnection) && $params->selectedConnection){
            $query -> where('ac.connection_type', $params -> selectedConnection);
        }

        if(isset($params->selectedVLAN) && $params->selectedVLAN){
            $query -> where('lwp.VLAN', $params -> selectedVLAN);
        }

        if (isset($params->startDate) && $params->startDate &&
            isset($params->endDate) && $params->endDate){
                $start_date = date('Y-m-d', strtotime($params->startDate));
                $start_date = new Carbon($start_date);
                $start_date = $start_date -> addDays(1);
                $end_date = date('Y-m-d', strtotime($params->endDate));
                $end_date = new Carbon($end_date);
                $end_date = $end_date -> addDays(2);
            $query -> whereBetween('lwp.updated_at', [$start_date, $end_date]);

        }

        if (isset($params->selectedAccount) && $params->selectedAccount){
            switch(true)
            {
                case count($params->selectedAccount) == 1:
                    if(isset($params -> selectedOption) && $params -> selectedOption)
                    {
                        $threshold = $this -> getThresholdByAccount(8,$params->selectedAccount[0]);
                        $query -> whereRaw('lwp.ram_usage '.$params -> selectedOption.' '.$threshold -> threshold);
                    }

                break;
                case count($params->selectedAccount) > 1:
                    $query -> where( function( $q ) use ($params) {
    
                        $q -> where(function ($que) use ($params) {


                            $threshold = $this -> getThresholdByAccount(8, $params->selectedAccount[0]);

                            $que -> whereRaw('lwp.ram_usage '.$params -> selectedOption.' '.$threshold -> threshold);
            
                        });
                        
                        foreach($params->selectedAccount as $sa)
                        {
                            $q -> orWhere(function ($que) use ($params, $sa) {

                                $threshold = $this -> getThresholdByAccount(8,$sa);
                
                                $que -> whereRaw('lwp.ram_usage '.$params -> selectedOption.' '.$threshold -> threshold);
                
                            });

                        }
            
                    });
                break;
            }
        }elseif(isset($params -> selectedOption) && $params -> selectedOption)
        {   
            $threshold = $this -> getThresholdByAccount(7,0);
            // print_r($threshold);
            $query -> whereRaw('lwp.ram_usage'.$params -> selectedOption.' '.$threshold -> threshold);
        }

        $query -> orderBy('lwp.updated_at','DESC');


        $final_data = json_encode(array(
            'data'  => $query->get(),
            'count' => $query->count()
        ));

        return $final_data;

    }

    public function queryUtilizationsRAM($params)
    {
        
        $query = $this->DB_READ->table('agent_connections as ac');
        $query -> select([
            'lwp.updated_at as Date Updated', 'ac.worker_id as Worker ID', 'ac.agent_name as Name', 'ac.agent_email as Email',
            'ac.account as Account', 'ac.station_name as Workstation','lwp.cpu as CPU','lwp.ram_usage as RAM Usage',
            'lwp.DISK as DISK','lwp.free_disk as Free Disk','lwp.cpu_util as CPU Utilization','lwp.ram as RAM'
        ]);

        $query -> leftJoin('logs_workstation_profile as lwp', function($join){
            $join -> on('lwp.worker_id','=','ac.worker_id');
        });

        $query -> leftJoin('accounts as a', function($join){
            $join -> on('a.name','=','ac.account');
        });

        if (isset($params->selectedAccount) && $params->selectedAccount){
            $query -> whereIn('ac.account', $params->selectedAccount);
        }

        if(isset($params->employee_id) && $params->employee_id){
            $query -> whereIn('ac.worker_id', $params -> employee_id);
        }

        if(isset($params->locationSelect) && $params->locationSelect){
            $query -> whereIn('ac.location', $params -> locationSelect);
        }

        if(isset($params->selectedConnection) && $params->selectedConnection){
            $query -> where('ac.connection_type', $params -> selectedConnection);
        }

        if(isset($params->selectedVLAN) && $params->selectedVLAN){
            $query -> where('lwp.VLAN', $params -> selectedVLAN);
        }

        if (isset($params->startDate) && $params->startDate &&
            isset($params->endDate) && $params->endDate){
                $start_date = date('Y-m-d', strtotime($params->startDate));
                $start_date = new Carbon($start_date);
                $start_date = $start_date -> addDays(1);
                $end_date = date('Y-m-d', strtotime($params->endDate));
                $end_date = new Carbon($end_date);
                $end_date = $end_date -> addDays(2);
            $query -> whereBetween('lwp.updated_at', [$start_date, $end_date]);

        }

        if (isset($params->selectedAccount) && $params->selectedAccount){
            switch(true)
            {
                case count($params->selectedAccount) == 1:
                    if(isset($params -> selectedOption) && $params -> selectedOption)
                    {
                        $threshold = $this -> getThresholdByAccount(8,$params->selectedAccount[0]);
                        $query -> whereRaw('lwp.ram '.$params -> selectedOption.' '.$threshold -> threshold);
                    }

                break;
                case count($params->selectedAccount) > 1:
                    $query -> where( function( $q ) use ($params) {
    
                        $q -> where(function ($que) use ($params) {


                            $threshold = $this -> getThresholdByAccount(8, $params->selectedAccount[0]);

                            $que -> whereRaw('lwp.ram '.$params -> selectedOption.' '.$threshold -> threshold);
            
                        });
                        
                        foreach($params->selectedAccount as $sa)
                        {
                            $q -> orWhere(function ($que) use ($params, $sa) {

                                $threshold = $this -> getThresholdByAccount(8,$sa);
                
                                $que -> whereRaw('lwp.ram '.$params -> selectedOption.' '.$threshold -> threshold);
                
                            });

                        }
            
                    });
                break;
            }
        }elseif(isset($params -> selectedOption) && $params -> selectedOption)
        {   
            $threshold = $this -> getThresholdByAccount(7,0);
            // print_r($threshold);
            $query -> whereRaw('lwp.ram'.$params -> selectedOption.' '.$threshold -> threshold);
        }

        $query -> orderBy('lwp.updated_at','DESC');


        $final_data = json_encode(array(
            'data'  => $query->get(),
            'count' => $query->count()
        ));

        return $final_data;

    }

    public function queryUtilizationsCPU($params)
    {
        // SET @ACCOUNT = "OPEXOTHER" COLLATE utf8mb4_unicode_ci,
        //     @STARTDATE = "2022-01-10" COLLATE utf8mb4_unicode_ci,
        //     @ENDDATE = "2022-08-01" COLLATE utf8mb4_unicode_ci;

        // SELECT 
        //     lwp.updated_at as `Date Updated`,
        //     ac.worker_id as `Worker ID`,
        //     ac.agent_name as `Name`,
        //     ac.agent_email as `Email`,
        //     ac.account as `Account`,
        //     ac.station_name as `Workstation`,
        //     lwp.cpu as `CPU`,
        //     lwp.cpu_util as `CPU Utilization`,
        //     lwp.ram as `RAM`,
        //     lwp.ram_usage as `RAM Usage`,
        //     lwp.DISK  as `Disk`,
        //     lwp.free_disk as `Free Disk`,
        //     CONCAT('Low ',
        //         IF ((lwp.cpu_util< mt1.threshold), 'CPU, ', ''),
        //         IF ((lwp.ram < mt2.threshold), 'RAM, ', ''),
        //         IF ((lwp.ram_usage < mt3.threshold), 'RAM Usage, ', ''),
        //         IF ((lwp.disk < mt4.threshold), 'Disk, ', ''),
        //         IF ((lwp.free_disk < mt5.threshold), 'Free Disk, ', '')
        //     ) AS `Remarks`
        // FROM agent_connections ac
        // JOIN logs_workstation_profile lwp ON lwp.worker_id = ac.worker_id
        // JOIN cnx_employees ce ON ce.employee_number = ac.worker_id 
        // JOIN accounts a ON a.name = ce.msa_client  
        // LEFT JOIN metrics_thresholds mt1 ON mt1.account_id = a.id AND mt1.report_id = 7 AND mt1.name = "CPU"
        // LEFT JOIN metrics_thresholds mt2 ON mt2.account_id = a.id AND mt2.report_id = 7 AND mt2.name = "RAM"
        // LEFT JOIN metrics_thresholds mt3 ON mt3.account_id = a.id AND mt3.report_id = 7 AND mt3.name = "RAM Usage"
        // LEFT JOIN metrics_thresholds mt4 ON mt4.account_id = a.id AND mt4.report_id = 7 AND mt4.name = "Disk"
        // LEFT JOIN metrics_thresholds mt5 ON mt5.account_id = a.id AND mt5.report_id = 7 AND mt5.name = "Free Disk"
        // WHERE lwp.updated_at BETWEEN @STARTDATE AND @ENDDATE 
        // AND ac.account = @ACCOUNT
        // AND ac.worker_id = '101943044'
        // -- THRESHOLD CONDITIONS
        // AND ((lwp.cpu_util < mt1.threshold)
        //     OR (lwp.ram < mt2.threshold)
        //     OR (lwp.ram_usage < mt3.threshold)
        //     OR (lwp.disk < mt4.threshold)
        //     OR (lwp.free_disk < mt5.threshold)
        // )
        // ORDER BY lwp.updated_at DESC
        $query = $this->DB_READ->table('agent_connections as ac');
        $query -> select([
            'lwp.updated_at as Date Updated', 'ac.worker_id as Worker ID', 'ac.agent_name as Name', 'ac.agent_email as Email',
            'ac.account as Account', 'ac.station_name as Workstation','lwp.cpu as CPU','lwp.ram as RAM','lwp.ram_usage as RAM Usage',
            'lwp.DISK as DISK','lwp.free_disk as Free Disk','lwp.cpu_util as CPU Utilization'
        ]);

        $query -> leftJoin('logs_workstation_profile as lwp', function($join){
            $join -> on('lwp.worker_id','=','ac.worker_id');
        });

        $query -> leftJoin('accounts as a', function($join){
            $join -> on('a.name','=','ac.account');
        });

        if (isset($params->selectedAccount) && $params->selectedAccount){
            $query -> whereIn('ac.account', $params->selectedAccount);
        }

        if(isset($params->employee_id) && $params->employee_id){
            $query -> whereIn('ac.worker_id', $params -> employee_id);
        }

        if(isset($params->locationSelect) && $params->locationSelect){
            $query -> whereIn('ac.location', $params -> locationSelect);
        }

        if(isset($params->selectedConnection) && $params->selectedConnection){
            $query -> where('ac.connection_type', $params -> selectedConnection);
        }

        if(isset($params->selectedVLAN) && $params->selectedVLAN){
            $query -> where('lwp.VLAN', $params -> selectedVLAN);
        }

        if (isset($params->startDate) && $params->startDate &&
            isset($params->endDate) && $params->endDate){
                $start_date = date('Y-m-d', strtotime($params->startDate));
                $start_date = new Carbon($start_date);
                $start_date = $start_date -> addDays(1);
                $end_date = date('Y-m-d', strtotime($params->endDate));
                $end_date = new Carbon($end_date);
                $end_date = $end_date -> addDays(2);
            $query -> whereBetween('lwp.updated_at', [$start_date, $end_date]);

        }

        
        if (isset($params->selectedAccount) && $params->selectedAccount){
            switch(true)
            {
                case count($params->selectedAccount) == 1:
                    if(isset($params -> selectedOption) && $params -> selectedOption)
                    {
                        $threshold = $this -> getThresholdByAccount(7,$params->selectedAccount[0]);
                        $query -> whereRaw('lwp.cpu_util '.$params -> selectedOption.' '.$threshold -> threshold);
                    }

                break;
                case count($params->selectedAccount) > 1:
                    $query -> where( function( $q ) use ($params) {
    
                        $q -> where(function ($que) use ($params) {


                            $threshold = $this -> getThresholdByAccount(7, $params->selectedAccount[0]);

                            $que -> whereRaw('lwp.cpu_util '.$params -> selectedOption.' '.$threshold -> threshold);
            
                        });
                        
                        foreach($params->selectedAccount as $sa)
                        {
                            $q -> orWhere(function ($que) use ($params, $sa) {

                                $threshold = $this -> getThresholdByAccount(7,$sa);
                
                                $que -> whereRaw('lwp.cpu_util '.$params -> selectedOption.' '.$threshold -> threshold);
                
                            });

                        }
            
                    });
                break;
            }
        }elseif(isset($params -> selectedOption) && $params -> selectedOption)
        {   
            $threshold = $this -> getThresholdByAccount(7,0);
            // print_r($threshold);
            $query -> whereRaw('lwp.cpu_util'.$params -> selectedOption.' '.$threshold -> threshold);
        }

        $query -> orderBy('lwp.updated_at','DESC');


        $final_data = json_encode(array(
            'data'  => $query->get(),
            'count' => $query->count()
        ));

        return $final_data;

    }

    public function queryResReqApps($params)
    {
        // SELECT
        //     aa.worker_id as worker_id,
        //     ce.firstname as first_name,
        //     ce.lastname as last_name,
        //     ce.email as email,
        //     aa.account as account,
        //     al.name as application_name,
        //     al.type as type,
        //     aa.updated_at as updated_at
        //     FROM agent_applications aa 
        //     JOIN applications_list al ON al.id = aa.application_id
        //     JOIN cnx_employees ce ON ce.employee_number = aa.worker_id
        //     WHERE aa.updated_at BETWEEN '2022-01-10' AND '2022-08-01'
        //         AND aa.account = 'BELKIN'
        //         AND aa.worker_id = '793768';
        $query = $this->DB_READ->table('agent_applications as aa');
        $query -> select([
            'aa.worker_id as Employee ID', 'ce.firstname as First Name', 'ce.lastname as Last Name', 'ce.email as Email',
            'aa.account as Account', 'al.name as Application Name', 'al.type as Type','aa.updated_at as Date Range'
        ]);
        $query -> leftJoin('applications_list as al', function($join){
            $join -> on('al.id','=','aa.application_id');
        });
        $query -> leftJoin('cnx_employees as ce', function($join){
            $join -> on('ce.employee_number','=','aa.worker_id');
        });
        if(isset($params->employee_id) && $params->employee_id){
            $query -> whereIn('aa.worker_id', $params -> employee_id);
        }
        if(isset($params->selectedAccount) && $params->selectedAccount){
            $query -> whereIn('aa.account', $params->selectedAccount);
        }
        if (isset($params->startDate) && $params->startDate &&
            isset($params->endDate) && $params->endDate){
                $start_date = date('Y-m-d', strtotime($params->startDate));
                $start_date = new Carbon($start_date);
                $start_date = $start_date -> addDays(1);
                $end_date = date('Y-m-d', strtotime($params->endDate));
                $end_date = new Carbon($end_date);
                $end_date = $end_date -> addDays(2);
            $query -> whereBetween('aa.updated_at', [$start_date, $end_date]);

        }
        $final_data = json_encode(array(
            'data'  => $query->get(),
            'count' => $query->count()
        ));

        return $final_data;

    }

    public function getReportType($id = null)
    {
        if($id==null)
        {
            $query =  ReportType::all();
    
            $results = array();
            foreach($query as $q)
            {
                $result = array(
                    'id' => $q->id,
                    'type' => $q->type
                );
                array_push($results,$result);
            }
        }else{
            $query = ReportType::where('id', $id);
            
            $results = $query  -> first();
        }
        // print_r($results);
        return $results;
    }

    public function getThresholdByID($thres_id)
    {
        $query = Threshold::where('id', $thres_id);
        return $query -> first();
    }

    public function getThresholdByAccount($report_id, $account)
    {
        $whereClause = $account==0?array('report_id' => $report_id, 'account_id' => 0):array('report_id' => $report_id, 'acnt.name' => $account);
        $query = Threshold::where($whereClause) -> select(['metrics_thresholds.id as mt_id','threshold','report_id', 'account_id','metrics_thresholds.name as mt_name']);
       if($account!=0)
       {
            $query -> addSelect('acnt.name as acnt_name');
            $query -> leftJoin('accounts as acnt', function($join){
                $join -> on('acnt.id','=','metrics_thresholds.account_id');
            });
       }

        if($query->count() > 0)
        {
            if($report_id==2 || $report_id==3){
                return $query -> get();
            }
            return $query -> first();
        }else{
            $query = Threshold::where(array('report_id' => $report_id, 'account_id' => 0));

            if($report_id==2 || $report_id==3){
                return $query -> get();
            }
            return $query -> first();
        }


    }

    public function getThreshold($code,$account)
    {
            $accountArray = explode(',',$account);
            $code = explode(',',$code);
        
            $query = Threshold::whereIn('report_id', $code)->select('metrics_thresholds.id', 'metrics_thresholds.name as thres_name','threshold');

            if($account!='null' && $account!=null)
            {
                $query -> leftJoin('accounts as acnt', function($join){
                            $join -> on('acnt.id','=','metrics_thresholds.account_id');
                        });
                $query->addSelect('acnt.name as acnt_name');
                $query -> whereIn('acnt.name', $accountArray);
            }else{
                $query->where('account_id', 0);
            }
            
            return $query->get();
        
        
    }

    public function getLocationByAccount($account)
    {
            $array = explode(',',$account);
        
            $query = AgentConnection::whereIn('account', $array) -> select('location');
            return $query->distinct()->get()->toArray();
        
        
    }

    public function queryPING($params)
    {
        $query = $this->DB_READ->table('agent_connections as ac');
        $query->select([
            'ac.worker_id as Employee_id','ac.agent_name as Agent Name', 'ac.agent_email as Agent Email', 'a.name as Account', 'wp.network_type as Agent',
            'monitoring.jitter as Jitter','monitoring.packet_loss as Packet Loss','monitoring.average_latency as Ping'
        ]);
        $query->leftJoin('workstation_profile AS wp', function( $join ){
            $join->on( 'wp.worker_id', '=', 'ac.worker_id')
            ->on('wp.id', '=', DB::raw("(SELECT max(id) from workstation_profile WHERE workstation_profile.worker_id = ac.worker_id AND redflag_id=0)"));
        });

        $query -> leftJoin('monitoring', function($join){
            $join -> on('monitoring.worker_id','=','ac.worker_id');
        });

        $query -> leftJoin('accounts as a', function($join){
            $join -> on('a.name','=','ac.account');
        });


        if (isset($params->employee_id) && $params->employee_id){
            $query->whereIn('ac.worker_id', $params->employee_id );
        }

        if(isset($params->locationSelect) && $params->locationSelect){
            $query -> whereIn('ac.location', $params -> locationSelect);
        }

        if(isset($params->selectedConnection) && $params->selectedConnection){
            $query -> where('ac.connection_type', $params -> selectedConnection);
        }

        if(isset($params->selectedVLAN) && $params->selectedVLAN){
            $query -> where('wp.VLAN', $params -> selectedVLAN);
        }

        if(isset($params->selectedAgent) && $params->selectedAgent){
            $query -> where('wp.network_type', $params -> selectedAgent);
        }

        if (isset($params->startDate) && $params->startDate &&
            isset($params->endDate) && $params->endDate){
                $start_date = date('Y-m-d', strtotime($params->startDate));
                $start_date = new Carbon($start_date);
                $start_date = $start_date -> addDays(1);
                $end_date = date('Y-m-d', strtotime($params->endDate));
                $end_date = new Carbon($end_date);
                $end_date = $end_date -> addDays(2);

            $query -> whereBetween("monitoring.created_at", [$start_date, $end_date]);
        }

        if (isset($params->selectedAccount) && $params->selectedAccount){
            switch(true)
            {
                case count($params->selectedAccount) == 1:
                    if(isset($params -> selectedOption) && $params -> selectedOption)
                    {
                        $threshold = $this -> getThresholdByAccount(4,$params->selectedAccount[0]);
                        $query -> whereRaw('monitoring.average_latency '.$params -> selectedOption.' '.$threshold -> threshold);
                    }

                break;
                case count($params->selectedAccount) > 1:
                    $query -> where( function( $q ) use ($params) {
    
                        $q -> where(function ($que) use ($params) {


                            $threshold = $this -> getThresholdByAccount(4, $params->selectedAccount[0]);

                            $que -> whereRaw('monitoring.average_latency '.$params -> selectedOption.' '.$threshold -> threshold);
            
                        });
                        
                        foreach($params->selectedAccount as $sa)
                        {
                            $q -> orWhere(function ($que) use ($params, $sa) {

                                $threshold = $this -> getThresholdByAccount(4,$sa);
                
                                $que -> whereRaw('monitoring.average_latency '.$params -> selectedOption.' '.$threshold -> threshold);
                
                            });

                        }
            
                    });
                break;
            }
        }elseif(isset($params -> selectedOption) && $params -> selectedOption)
        {   
            $threshold = $this -> getThresholdByAccount(4,0);
            // print_r($threshold);
            $query -> whereRaw('monitoring.average_latency '.$params -> selectedOption.' '.$threshold -> threshold);
        }

        $final_data = json_encode(array(
            'data'  => $query->get(),
            'count' => $query->count()
        ));

        return $final_data;
    }

    public function getReportQuery($table, $params)
    {
            $query = $this->DB_READ->table('agent_connections as ac');
            $query->select([
                'ac.agent_name as Agent Name', 'ac.agent_email as Agent Email', 'wp.station_number as Workstation ID', 'ac.account as Account', 'ac.location AS Location', 'wp.host_ip_address as IP Address', 'wp.subnet as Subnet',
                'wp.DNS_1 as DNS 1','wp.DNS_2 as DNS 2', 'wp.gateway as Gateway', 'wp.host_name as Hostname','wp.isp_fullname as ISP','ac.worker_id AS Employee ID',
                'ac.country as Country'
            ]);
            $query->leftJoin('workstation_profile AS wp', function( $join ){
                $join->on( 'wp.worker_id', '=', 'ac.worker_id')
                ->on('wp.id', '=', DB::raw("(SELECT max(id) from workstation_profile WHERE workstation_profile.worker_id = ac.worker_id AND redflag_id=0)"));
            });
            $selected_table = self::REPORT_TYPE_REF[$table];
            $to_join = $selected_table.'.'.'worker_id';
            $additional_fields = $selected_table.'.'.'*';
            $query->addSelect($additional_fields);
            $query->leftJoin($selected_table, $to_join, '=', 'ac.worker_id');

            if (isset($params->employee_id) && $params->employee_id){
                $query->whereIn('ac.worker_id', $params->employee_id );
            }

            if (isset($params->selectedAccount) && $params->selectedAccount){
                $query -> whereIn('ac.account', $params->selectedAccount);
            }

            if(isset($params->locationSelect) && $params->locationSelect){
                $query -> whereIn('ac.location', $params -> locationSelect);
            }

            if(isset($params->selectedConnection) && $params->selectedConnection){
                $query -> where('ac.connection_type', $params -> selectedConnection);
            }

            if (isset($params->startDate) && $params->startDate &&
                isset($params->endDate) && $params->endDate){
                $start_date = date('Y-m-d', strtotime($params->startDate));
                $start_date = new Carbon($start_date);
                $start_date = $start_date -> addDays(1);
                $end_date = date('Y-m-d', strtotime($params->endDate));
                $end_date = new Carbon($end_date);
                $end_date = $end_date -> addDays(2);

                $query -> whereBetween("logs_mos.updated_at", [$start_date, $end_date]);
            }

            if($table=="mos")
            {

                $query -> leftJoin('metrics_thresholds as mt', function($join){
                    $join -> on('ac.id','=','mt.account_id');
                });

                if (isset($params->selectedAccount) && $params->selectedAccount){
                    switch(true)
                    {
                        case count($params->selectedAccount) == 1:
                            if(isset($params -> selectedOption) && $params -> selectedOption)
                            {
                                $threshold = $this -> getThresholdByAccount(1,$params->selectedAccount[0]);
                                $query -> whereRaw('logs_mos.mos '.$params -> selectedOption.' '.$threshold -> threshold);
                            }

                        break;
                        case count($params->selectedAccount) > 1:
                            $query -> where( function( $q ) use ($params) {
            
                                $q -> where(function ($que) use ($params) {
        
        
                                    $threshold = $this -> getThresholdByAccount(1, $params->selectedAccount[0]);
        
                                    // echo 'mt.id ="'.$threshold -> id.'" AND logs_mos.mos '.$params -> selectedOption.' '.$threshold -> threshold;
                    
                                    $que -> whereRaw('logs_mos.mos '.$params -> selectedOption.' '.$threshold -> threshold);
                    
                                });
                                
                                foreach($params->selectedAccount as $sa)
                                {
                                    $q -> orWhere(function ($que) use ($params, $sa) {
        
                                        $threshold = $this -> getThresholdByAccount(1,$sa);
                        
                                        $que -> whereRaw('logs_mos.mos '.$params -> selectedOption.' '.$threshold -> threshold);
                        
                                    });
        
                                }
                    
                            });
                        break;
                    }
                }elseif(isset($params -> selectedOption) && $params -> selectedOption)
                {   
                    $threshold = $this -> getThresholdByAccount(1,0);
                    // print_r($threshold);
                    $query -> whereRaw('logs_mos.mos '.$params -> selectedOption.' '.$threshold -> threshold);
                }

                $query -> orderBy('logs_mos.updated_at','DESC');
            }

            $final_data = json_encode(array(
                'data'  => $query->get(),
                'count' => $query->count()
            ));

            // print_r($final_data);

            return $final_data;
            
    }


    public function exportReport($data_array, $list_of_headers )
    {
        $spreadsheet = new Spreadsheet();
        $i = 0;
        foreach($data_array as $key => $reports)
        {
            $rt = $this -> getReportType($key);
            $i++;
            if($i==1)
            {
                $sheet[$key] = $spreadsheet->getActiveSheet();
                $sheet[$key] -> setTitle(strtoupper($rt -> code));
            }else{
                $sheet[$key] = $spreadsheet->createSheet()->setTitle(strtoupper($rt -> code));
            }
            $reports = json_decode(json_encode($reports),true);

            $rowHeader = 1;
            $colHeader = 1;
            $colLetters = 'A';

            foreach($list_of_headers[$key] as $sheetHeader)
            {
                $sheet[$key]->getStyleByColumnAndRow($colHeader, $rowHeader)->getFont()->setBold(true);
                $sheet[$key]->setCellValueByColumnAndRow($colHeader, $rowHeader, $sheetHeader);
                $sheet[$key]->getColumnDimension($colLetters)->setAutoSize(true);
                $colHeader++;
                $colLetters++;

            }

            $row = 2;

            foreach( $reports as $r)
            {
                $col = 1;

                foreach($r as $val)
                {
                    $sheet[$key]->setCellValueByColumnAndRow($col, $row, $val);
                    $col++;
                }
                $row++;
            }

        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');
    
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer -> save('php://output');
    }


    public function exportToExcel( $data_array, $list_of_headers) {

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
        $writer -> save('php://output');
    }

    public function sendDailyReports() {
        $current_date = Carbon::now()->format('Ymd');
        Log::info('START CRON DAILY REPORTS '.Carbon::now()->toDateTimeString());
        
        if (getenv('ACCOUNT_MAIL_ACTIVE') == 'true') {

            $specific_accounts = explode(',', getenv('MAIL_ACCOUNT'));
            
            $specific_users = $this->DB_READ
                ->table('users')
                ->select('email', 'account_access', 'firstname')
                ->whereNotNull('account_access')
                ->where('account_access', '!=', '');
            
            $specific_users->where( function( $q ) use ($specific_accounts) {
                foreach ($specific_accounts as $account) {
                    $q->orWhere('account_access', 'like', "%$account%");
                }
            });
            
            $list_of_users = $specific_users->get();
            
        } else {
            $list_of_users = $this->DB_READ
                ->table('users')
                ->select('email', 'account_access', 'firstname')
                ->whereNotNull('account_access')
                ->where('account_access', '!=', '')->get();
        }

        foreach($list_of_users as $user_data) {
           
            $data_per_tab = array();
            $report_types_with_data = array();
        
            $email = $user_data->email;

            $threshold_name = [
                'Download Speed', 'Upload Speed', 'Ping', 'CPU', 'Disk', 'RAM'
            ];

            $list_of_accounts = $user_data->account_access ? explode(",",$user_data->account_access) : [] ;
            
            // list of account that does not exist in metrics_thresholds
            $accounts_not_exist = $this->DB_READ
                ->table('accounts')
                ->select('accounts.name', 'accounts.id')
                ->leftJoin('metrics_thresholds', 'metrics_thresholds.account_id', '=', 'accounts.id')
                ->whereIn('accounts.name', $list_of_accounts)
                ->where('account_id', null)
                ->groupBy('accounts.name', 'account_id')->get();
            
            // add accounts in metrics_thresholds using default threshold
            if(count($accounts_not_exist)) {
                foreach ($accounts_not_exist as $value) {
                    $this->defaultVauesOfAccountThreshold($value);
                }
            }
            
            $list_of_thresholds_per_account = $this->DB_READ
                ->table('metrics_thresholds')    
                ->select('metrics_thresholds.*', 'accounts.name as account_name')
                ->leftJoin('accounts', 'accounts.id', '=', 'metrics_thresholds.account_id')
                ->whereIn('metrics_thresholds.name', $threshold_name)
                ->whereIn('accounts.name', $list_of_accounts)->get()->toArray();
            
            $query = $this->DB_READ
                ->table('smart_report_types')
                ->select('smart_report_types.id as report_type_id');

            $query->leftJoin('report_types_per_user AS rtpu', function( $join ) use ($email){
                $join->on( 'rtpu.report_type_id', '=', 'smart_report_types.id')
                ->on('rtpu.email', '=', DB::raw("'$email'"));
            });

            $query->where('rtpu.report_type_id', null);

            $report_types = $query->get();
 
            foreach ($report_types as $value) {    
                
                switch ($value->report_type_id) {
                    case 1:
                        $data_result = $this->speedtestThreshold($list_of_accounts);
                        if(count($data_result) > 0){
                            array_push($data_per_tab, $data_result);
                            array_push($report_types_with_data, $value->report_type_id);
                        }
                    break;

                    case 2:
                        $data_result = $this->applicationThreshold($list_of_accounts);
                        if(count($data_result) > 0){
                            array_push($data_per_tab, $data_result);
                            array_push($report_types_with_data, $value->report_type_id);
                        }
                    break;

                    case 3:
                        $data_result = $this->offlineAgents($list_of_accounts);
                        if(count($data_result) > 0){
                            array_push($data_per_tab, $data_result);
                            array_push($report_types_with_data, $value->report_type_id);
                        }
                    break;

                    case 4:
                        $data_result = $this->utilization($list_of_accounts);
                        if(count($data_result) > 0){
                            array_push($data_per_tab, $data_result);
                            array_push($report_types_with_data, $value->report_type_id);
                        }
                    break;
           
                    default:

                        $data_result = $this->restrictedApplications($list_of_accounts);
                        if(count($data_result) > 0){
                            array_push($data_per_tab, $data_result);
                            array_push($report_types_with_data, '5');
                        }
                        
                        $data_result = $this->requiredApplications($list_of_accounts);
                        if(count($data_result) > 0){
                            array_push($data_per_tab, $data_result);
                            array_push($report_types_with_data, '6');
                        }
                }
                  
            }

            if (count($report_types->toArray()) > 0 ){
                if(array_filter($data_per_tab)) {
                    $link = $this->urlGenerator->temporarySignedRoute(
                        'disabled', Carbon::now()->addDays(1), ['email' => $user_data->email]
                    );
                    
                    $split_link = explode('reports/',$link);

                    $parse = parse_url($split_link[0]);
                    $host = $parse['host'];
                    $full_link = 'https://'.$host.'/reports/'.$split_link[1];
                    
                    try {
                        
                        if (getenv('LOCAL_MAIL_ACTIVE') == 'true') {
                            // local test
                            $default_cc_users = explode(',', getenv('MAIL_CC_TESTING'));
                            Mail::to(getenv('MAIL_TO_TESTING'))
                            ->cc($default_cc_users)
                            ->send(new AutoSmartReportsMail($data_per_tab, $report_types_with_data, $user_data, $list_of_thresholds_per_account, $full_link, $user_data->account_access));
                            
                            File::delete(public_path("sentrycx_daily_reports_$current_date.xlsx"));
                            break;
                        }

                        if (getenv('STAGING_MAIL_ACTIVE') == 'true') {
                            // staging test
                            $default_cc_users = explode(',', getenv('MAIL_CC_TESTING'));
                            Mail::to(getenv('MAIL_TO_TESTING'))
                            ->cc($default_cc_users)
                            ->send(new AutoSmartReportsMail($data_per_tab, $report_types_with_data, $user_data, $list_of_thresholds_per_account, $full_link, $user_data->account_access));
                        }

                        if (getenv('PROD_MAIL_ACTIVE') == 'true') {
                            // for prod
                            $default_bcc_users = explode(',', getenv('MAIL_BCC_PROD'));
                            Mail::to($user_data->email)
                            ->bcc($default_bcc_users)
                            ->send(new AutoSmartReportsMail($data_per_tab, $report_types_with_data, $user_data, $list_of_thresholds_per_account, $full_link, $user_data->account_access));
                        }

                        File::delete(public_path("sentrycx_daily_reports_$current_date.xlsx"));
                    } catch(\Exception $error) {
                        
                    }
                }
            }

        }

        Log::info('END CRON DAILY REPORTS '.Carbon::now()->toDateTimeString());

    }

    private function speedtestThreshold($account_access) {
        
        $result = array();

        $lastElement = end($account_access);
        
        if (count($account_access) > 0) {
            $concat_accounts = "";
            foreach($account_access as $value) {  

                $raw_account = " bb.account = '$value'";

                if($value != $lastElement) {
                    $raw_account = $raw_account." OR";
                } else {
                    $raw_account = $raw_account;
                }
                
                $concat_accounts .= $raw_account;
            }
            
        }

        try {
            $query = $this->DB_READ
                ->select("SELECT a.worker_id AS 'Worker ID',
                b.agent_name AS 'Agent Name',
                b.agent_email AS 'Email',
                b.station_name AS 'Station',
                b.location AS 'Location',
                b.account AS 'Account',
                b.country AS 'Country',
                a.connection_type AS 'Connection Type',
                ROUND(c.latest_ave_down_speed, 2) AS 'Latest Download Speed (Mbps)',
                ROUND(c.latest_ave_up_speed, 2) AS 'Latest Upload Speed (Mbps)',
                ROUND(AVG(a.download_speed), 2) AS 'Avg. Download Speed (Mbps)',
                ROUND(AVG(a.upload_speed), 2) AS 'Avg. Upload Speed (Mbps)',
                c.download_threshold AS 'Download Threshold (Mbps)',
                c.upload_threshold AS 'Upload Threshold (Mbps)'
            FROM logs_speedtest a 
            LEFT JOIN agent_connections b ON b.worker_id = a.worker_id
            JOIN (
                SELECT aa.worker_id,
                    AVG(CAST(REGEXP_REPLACE(aa.download_speed, '[^0-9.]', '')+0 AS DECIMAL(8, 2))) AS latest_ave_down_speed,
                    AVG(CAST(REGEXP_REPLACE(aa.upload_speed, '[^0-9.]', '')+0 AS DECIMAL(8, 2))) AS latest_ave_up_speed,
                    dd.threshold AS download_threshold,
                    ee.threshold AS upload_threshold
                    FROM logs_speedtest aa
                    LEFT JOIN agent_connections bb ON aa.worker_id = bb.worker_id
                    LEFT JOIN accounts cc ON bb.account = cc.name
                    LEFT JOIN metrics_thresholds dd ON cc.id = dd.account_id AND dd.name = 'Download Speed'
                    LEFT JOIN metrics_thresholds ee ON cc.id = ee.account_id AND ee.name = 'Upload Speed'
                    WHERE aa.created_at BETWEEN DATE_SUB(NOW(), INTERVAL 1 DAY) AND NOW()
                        AND (aa.download_speed != '' AND aa.upload_speed != '')
                        AND ($concat_accounts)
                    GROUP BY aa.worker_id, dd.threshold, ee.threshold
                    HAVING AVG(CAST(REGEXP_REPLACE(aa.download_speed, '[^0-9.]', '')+0 AS DECIMAL(8, 2))) < dd.threshold
                        OR AVG(CAST(REGEXP_REPLACE(aa.upload_speed, '[^0-9.]', '')+0 AS DECIMAL(8, 2))) < ee.threshold
                ) c ON a.worker_id = c.worker_id
                WHERE a.created_at BETWEEN DATE_SUB(NOW(), INTERVAL 7 DAY) AND NOW()
                    AND (a.download_speed != '' AND a.upload_speed != '')
                GROUP BY a.worker_id, b.agent_name, b.agent_email, b.station_name, b.location, b.account, b.country, a.connection_type, c.latest_ave_down_speed, c.latest_ave_up_speed, c.download_threshold, c.upload_threshold
                HAVING AVG(CAST(REGEXP_REPLACE(a.download_speed, '[^0-9.]', '')+0 AS DECIMAL(8, 2))) < c.download_threshold
                    OR AVG(CAST(REGEXP_REPLACE(a.upload_speed, '[^0-9.]', '')+0 AS DECIMAL(8, 2))) < c.upload_threshold
                ORDER BY b.account ASC");
            
                $result = collect($query)->map(function($x){ return (array) $x; })->toArray();


        } catch(\Exception $error) {

            $result = array();
            
        }
        
        return $result;

    }

    private function applicationThreshold($account_access) 
    {
        $result = array();

        $lastElement = end($account_access);
        
        if (count($account_access) > 0) {
            $concat_accounts = "";
            foreach($account_access as $value) {  

                $raw_account = " bb.account = '$value'";

                if($value != $lastElement) {
                   $raw_account = $raw_account." OR";
                } else {
                    $raw_account = $raw_account;
                }
                
                $concat_accounts .= $raw_account;
            }
           
        }

        try {

            $query = $this->DB_READ
                ->select("SELECT 
                a.worker_id AS 'Worker ID',
                b.agent_name AS 'Agent Name',
                b.agent_email AS 'Email',
                b.station_name AS 'Station',
                b.location AS 'Location',
                b.account AS 'Account',
                b.country AS 'Country',
                a.application AS 'Application',
                c.latest_ping AS 'Latest Ping (ms)',
                ROUND(AVG(average_latency), 2) AS 'Ave. Ping (ms)',
                c.threshold AS 'Ping Threshold (ms)'
            FROM monitoring a
            LEFT JOIN agent_connections b ON a.worker_id = b.worker_id
            JOIN (
                SELECT aa.worker_id,
                    aa.application,
                    ROUND(AVG(average_latency), 2) AS latest_ping,
                    dd.threshold
                FROM monitoring aa
                LEFT JOIN agent_connections bb ON aa.worker_id = bb.worker_id
                LEFT JOIN accounts cc ON bb.account = cc.name
                LEFT JOIN metrics_thresholds dd ON cc.id = dd.account_id
                WHERE aa.created_at BETWEEN DATE_SUB(NOW(), INTERVAL 1 DAY) AND NOW()
                    AND ($concat_accounts)
                    AND dd.name LIKE 'Ping'
                GROUP BY aa.worker_id, aa.application, dd.threshold
                HAVING AVG(average_latency) > dd.threshold
            ) c ON a.worker_id = c.worker_id AND a.application = c.application
            WHERE a.created_at BETWEEN DATE_SUB(NOW(), INTERVAL 7 DAY) AND NOW()
            GROUP BY a.worker_id, a.application, b.agent_name, b.agent_email, b.station_name, b.location,
            b.account, b.country, c.latest_ping, c.threshold
            HAVING AVG(average_latency) > c.threshold
            ORDER BY b.account ASC");

            $result = collect($query)->map(function($x){ return (array) $x; })->toArray();
        
        } catch(\Exception $error) {
            $result = array();
        }

        return $result;
    }

    private function offlineAgents($account_access) {

        $final_data = array();

        try {

            $data = $this->DB_READ
                ->table('agent_connections')
                ->select(
                'agent_name as Name', 
                'agent_email as Email',
                'agent_connections.worker_id as Employee ID',  
                'account as Account', 
                'station_number as Workstation', 
                'agent_connections.location as Location', 
                'agent_connections.updated_at as Last Online'
            );

            $data->addSelect(DB::raw("DATEDIFF(NOW(), agent_connections.updated_at) AS 'Days Since Last Online'"));

            $data->leftJoin('workstation_profile AS wp', function( $join ){
                $join->on( 'wp.worker_id', '=', 'agent_connections.worker_id')
                ->on('wp.id', '=', DB::raw("(SELECT max(id) from workstation_profile WHERE workstation_profile.worker_id = agent_connections.worker_id AND redflag_id=0)"));
            });

            if (count($account_access) > 0) {
                $data->whereIn('account', $account_access);
            }

            $data->where('is_admin', FALSE);
            $data->where('is_active', FALSE);
            $data->where('redflag_id', 0);
            $data->orderBy('agent_connections.updated_at', 'DESC');

            $inactive_days = date("Y-m-d H:i:s", strtotime( getenv('AGENT_INACTIVE_DAYS') . ' days'));

            $for_30_days = date("Y-m-d H:i:s", strtotime( -30 . ' days'));

            $data->where('agent_connections.updated_at', '>', $for_30_days );

            $data->where('agent_connections.updated_at', '<', $inactive_days );

            $query_result = $data->get();

            $final_data = json_decode(json_encode($query_result), true);

        } catch(\Exception $error) {
            $final_data = array();
        }

        return $final_data;
    }

    private function utilization($account_access) {

        $result = array();

        $lastElement = end($account_access);
        
        if (count($account_access) > 0) {
            $concat_accounts = "";
            foreach($account_access as $value) {  

                $raw_account = " bb.account = '$value'";

                if($value != $lastElement) {
                   $raw_account = $raw_account." OR";
                } else {
                    $raw_account = $raw_account;
                }
                
                $concat_accounts .= $raw_account;
            }
           
        }
       
        try {
           
            $query = $this->DB_READ
                ->select("SELECT 
                a.worker_id AS 'Worker ID',
                b.agent_name  AS 'Agent Name',
                b.agent_email AS 'Email',
                b.station_name  AS 'Station',
                b.location AS 'Location',
                b.account  AS 'Account',
                b.country AS 'Country',
                ROUND(c.latest_ram_usage, 2) AS 'Latest RAM Usage (%)',
                ROUND(c.latest_disk_usage, 2) AS 'Latest Disk Usage (%)',
                ROUND(c.latest_cpu_util, 2) AS 'Latest CPU Util. (%)',
                ROUND(AVG(a.ram_usage), 2) AS 'Ave. RAM Usage (%)',
                ROUND(AVG(a.free_disk), 2) AS 'Ave. Disk Usage (%)',
                ROUND(AVG(a.cpu_util), 2) AS 'Ave. CPU Util. (%)',
                c.ram_threshold AS 'RAM Threshold',
                c.disk_threshold AS 'Disk Threshold',
                c.cpu_threshold AS 'CPU Threshold',
                CONCAT('Above Threshold: ',
                    IF(AVG(a.ram_usage) > c.ram_threshold, 'RAM ', ''),
                    IF(AVG(a.free_disk) > c.disk_threshold, 'Disk ', ''),
                    IF(AVG(a.cpu_util) > c.cpu_threshold, 'CPU', '')) AS 'Remarks'
            FROM logs_workstation_profile a	
            LEFT JOIN agent_connections b ON a.worker_id = b.worker_id
            JOIN (
                SELECT aa.worker_id,
                    AVG(aa.ram_usage) AS latest_ram_usage,
                    AVG(aa.free_disk) AS latest_disk_usage,
                    AVG(aa.cpu_util) AS latest_cpu_util,
                    dd.threshold AS ram_threshold,
                    ee.threshold AS disk_threshold,
                    ff.threshold AS cpu_threshold
                FROM logs_workstation_profile aa
                LEFT JOIN agent_connections bb ON aa.worker_id = bb.worker_id
                LEFT JOIN accounts cc ON bb.account = cc.name
                LEFT JOIN metrics_thresholds dd ON cc.id = dd.account_id AND dd.name = 'RAM'
                LEFT JOIN metrics_thresholds ee ON cc.id = ee.account_id AND ee.name = 'Disk'
                LEFT JOIN metrics_thresholds ff ON cc.id = ff.account_id AND ff.name = 'CPU'
                WHERE aa.created_at BETWEEN DATE_SUB(NOW(), INTERVAL 1 DAY) AND NOW()
                    AND ($concat_accounts)
                GROUP BY aa.worker_id, dd.threshold, ee.threshold, ff.threshold
                HAVING AVG(ram_usage) > dd.threshold
                    OR AVG(free_disk) > ee.threshold
                    OR AVG(cpu_util) > ff.threshold
            ) c ON c.worker_id = a.worker_id
            WHERE a.created_at BETWEEN DATE_SUB(NOW(), INTERVAL 7 DAY) AND NOW()
            GROUP BY a.worker_id, b.agent_name, b.agent_email, b.station_name, b.location, b.account, b.country, c.latest_ram_usage, c.latest_disk_usage, c.latest_cpu_util, c.ram_threshold, c.disk_threshold, c.cpu_threshold
            HAVING AVG(a.ram_usage) > c.ram_threshold
                OR AVG(a.free_disk) > c.disk_threshold
                OR AVG(a.cpu_util) > c.cpu_threshold");

        $result = collect($query)->map(function($x){ return (array) $x; })->toArray();

        } catch(\Exception $error) {
            $result = array();
        }

        return $result;
    }

    private function restrictedApplications($account_access) {

        try {
            $data = $this->DB_READ
                ->table('agent_applications')
                ->select(
                'agent_name as Name', 
                'agent_email as Email', 
                'agent_connections.worker_id as Employee ID',
                'agent_connections.account as Account', 
                'agent_connections.station_name as Workstation',
                'agent_connections.location as Location', 
                'applications_list.name as App Name', 
                'applications_list.type as Type'
            );

            $data->leftJoin('agent_connections', 'agent_connections.worker_id', '=', 'agent_applications.worker_id');
            $data->leftJoin('applications_list', 'applications_list.id', '=', 'agent_applications.application_id');
        
            if (count($account_access) > 0) {
                $data->whereIn('agent_applications.account', $account_access);
            }

            $for_30_days = date("Y-m-d H:i:s", strtotime( -30 . ' days'));

            $data->where('applications_list.type', 'Restricted');

            $data->where('agent_applications.created_at', '>', $for_30_days );

            $query_result = $data->get()->toArray();

            $final_data = json_decode(json_encode($query_result), true);

        } catch(\Exception $error) {

            $final_data = array();
        }

        return $final_data;
    }

    private function requiredApplications($account_access) {

        try {
            $data = $this->DB_READ
                ->table('agent_applications')
                ->select(
                'agent_name as Name', 
                'agent_email as Email', 
                'agent_connections.worker_id as Employee ID',
                'agent_connections.account as Account', 
                'agent_connections.station_name as Workstation',
                'agent_connections.location as Location', 
                'applications_list.name as App Name', 
                'applications_list.type as Type'
            );

            $data->leftJoin('agent_connections', 'agent_connections.worker_id', '=', 'agent_applications.worker_id');
            $data->leftJoin('applications_list', 'applications_list.id', '=', 'agent_applications.application_id');
        
            if (count($account_access) > 0) {
                $data->whereIn('agent_applications.account', $account_access);
            }

            $for_30_days = date("Y-m-d H:i:s", strtotime( -30 . ' days'));

            $data->where('applications_list.type', 'Required');

            $data->where('agent_applications.created_at', '>', $for_30_days );

            $query_result = $data->get()->toArray();

            $final_data = json_decode(json_encode($query_result), true);

        } catch(\Exception $error) {

            $final_data = array();
        }

        return $final_data;
    }

    public function updateEmailNotifications($request) {

        $array_data = array();
       
        $email = $request->input('email');
        $speedtest = $request->input('speedtest');
        $application = $request->input('application');
        $offline = $request->input('offline');
        $utilization = $request->input('utilization');
        $required = $request->input('required');

        if (!$speedtest) {
            array_push($array_data, ['report_type_id' => 1, 'email' => $email]);
        } 
        
        if (!$application) {
            array_push($array_data, ['report_type_id' => 2, 'email' => $email]);
        } 
        
        if (!$offline) {
            array_push($array_data, ['report_type_id' => 3, 'email' => $email]);
        } 
        
        if (!$utilization) {
            array_push($array_data, ['report_type_id' => 4, 'email' => $email]);
        } 
        
        if (!$required) {
            array_push($array_data, ['report_type_id' => 5, 'email' => $email]);
        };
        
        ReportTypePerUser::where('email', $email)->delete();

        ReportTypePerUser::insert($array_data); 
    }

    private function defaultVauesOfAccountThreshold($data) {
        foreach(self::DEFAULT_THRESHOLDS as $value) {
            DB::table('metrics_thresholds')->insert(
                [
                       'report_id'  => $value['report_id'], 
                       'account_id' => $data->id,
                       'name'       => $value['name'],
                       'threshold'  => $value['threshold']
                ]
           );
        }
    }
}