<?php 

namespace App\Modules\Workday\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Modules\Workday\Models\CnxEmployeeUnlisted;
use App\Modules\Workday\Models\CnxEmployees;
use Log;

class EmployeeService {

    /** @var String $sortBy */
    protected $sortBy = null;
    protected $sortOrder = 'asc';

    public function setSort($field, $order){
        $this -> sortBy = $field;
        $this -> sortOrder =  strtolower( $order );
    }

    /**
     *
     * Retrieve the workstation profile
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function get( $workerID = 0){
        return DB::table('cnx_employees') -> select(
            'firstname','lastname','email','employee_number','country',
            'programme_msa','supervisor_full_name','supervisor_email_id',
            'location_name','lob','msa_client','job_profile'
        ) -> where('employee_number', $workerID)
        -> limit(1)
        -> get () ;
    }

    public function getEmployeeDetails( $workerID = 0){
        return DB::table('cnx_employees') -> select(
            'firstname','lastname','email','employee_number','country',
            'programme_msa','supervisor_full_name','supervisor_email_id',
            'location_name','lob','msa_client','job_profile'
        ) -> where('employee_number', $workerID)
        -> first () ;
    }

    /**
     *
     * Save to unlisted employees
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function logUnlistedEmployee($username, $subnet = ''){
        $employee = new CnxEmployeeUnlisted();
        $employee -> username = $username;
        $employee -> subnet   = $subnet;
        return $employee -> save();
    }

    /** 
     *
     * Retrieve all the resigned employee from workday data
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function getResignedEmployee(){
        $today = Carbon::now();

        return DB::table('cnx_employees AS ce') 
            -> leftJoin('potential_triggers AS pt', 'pt.worker_id', '=', 'ce.employee_number')
            -> select(
                'ce.firstname','ce.lastname','ce.email','ce.employee_number','ce.country',
                'ce.programme_msa','ce.supervisor_full_name','ce.supervisor_email_id',
                'ce.location_name','ce.lob','ce.msa_client','ce.job_profile', 'resignation_date'
            )
            -> where('resignation_date', '<', $today )
            -> whereNull('pt.id')
            -> get();
    }

    /**
     *
     * Retrieve all the employee that has upadted resignation date
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function getUpdatedResignation(){
        return DB::table('potential_triggers  AS pt') 
            -> leftJoin('cnx_employees AS ce', 'pt.worker_id', '=', 'ce.employee_number')
            -> select(
                'ce.firstname','ce.lastname','ce.email','ce.employee_number','ce.country',
                'ce.programme_msa','ce.supervisor_full_name','ce.supervisor_email_id',
                'ce.location_name','ce.lob','ce.msa_client','ce.job_profile', 'resignation_date'
            )
            -> whereRaw('pt.datetime_triggered <> ce.resignation_date' )
            -> orWhereNull('ce.resignation_date')
            -> get();
    }

    /**
     *
     * Insert Cnx Employees
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function insertCnxEmployees(){
        // insert
        // $new_data = array();

        // $new_cnx_employees = DB::table('udw_migration as udw')
        //     -> select('udw.*')
        //     -> leftJoin('cnx_employees AS ce', 'ce.employee_number', '=', 'udw.employee_number') 
        //     -> whereNull('ce.employee_number')
        //     -> get();
  
        // $new_cnx_employees_array = json_decode($new_cnx_employees, true);

        // foreach( $new_cnx_employees_array as $value) {

        //     $new_data[] = [
        //         'firstname' => $value['firstname'],
        //         'lastname' => $value['lastname'],
        //         'middle_name' => $value['middle_name'],
        //         'email' => $value['email'],
        //         'employee_number' => $value['employee_number'],
        //         'programme_msa' => $value['programme_msa'],
        //         'msa_client' => $value['msa_client'],
        //         'dept_id' => $value['dept_id'],
        //         'location_name' => $value['location'],
        //         'building' => $value['location_name'],
        //         'country' => $value['country'],
        //         'job_level' => $value['job_level'],
        //         'supervisor_id' => $value['supervisor_id'],
        //         'supervisor_full_name' => $value['supervisor_full_name'],
        //         'supervisor_email_id' => $value['supervisor_email_id'],
        //         'management_level' => $value['management_level'],
        //         'compensation_grade' => $value['compensation_grade'],
        //         'status' => $value['status'],
        //         'job_profile' => $value['job_profile'],
        //         'lob' => $value['lob'],
        //         'primary_address_city' => $value['PRIMARY_ADDRESS_CITY'],
        //         'sam_account_name' => $value['LAN_ID'],
        //         'job_family' => $value['JOB_FAMILY'],
        //         'region' => $value['REGION']
        //     ];
        // }
        
        // DB::table('cnx_employees')->insert($new_data);

        DB::insert('INSERT INTO cnx_employees (firstname,lastname,middle_name,email,employee_number,programme_msa,msa_client,dept_id,location_name,building,country,job_level,supervisor_id,supervisor_full_name,supervisor_email_id,management_level,compensation_grade,`status`,job_profile,lob,primary_address_city,sam_account_name,job_family,region)
            SELECT
            udw_migration.firstname as firstname,
            udw_migration.lastname as lastname,
            udw_migration.middle_name as middle_name,
            udw_migration.email as email,
            udw_migration.employee_number as employee_number,
            udw_migration.programme_msa as programme_msa,
            udw_migration.msa_client as msa_client,
            udw_migration.dept_id as dept_id,
            udw_migration.location as location_name,
            udw_migration.location_name as building,
            udw_migration.country as country,
            udw_migration.job_level as job_level,
            udw_migration.supervisor_id as supervisor_id,
            udw_migration.supervisor_full_name as supervisor_full_name,
            udw_migration.supervisor_email_id as supervisor_email_id,
            udw_migration.management_level as management_level,
            udw_migration.compensation_grade as compensation_grade,
            udw_migration.status as status,
            udw_migration.job_profile as job_profile,
            udw_migration.lob as lob,
            udw_migration.PRIMARY_ADDRESS_CITY as primary_address_city,
            udw_migration.LAN_ID as sam_account_name,
            udw_migration.JOB_FAMILY as job_family,
            udw_migration.REGION as region
            FROM udw_migration
            LEFT JOIN cnx_employees ON cnx_employees.`employee_number` = udw_migration.`employee_number`
            WHERE cnx_employees.`employee_number` IS NULL');

    echo "done";
 
    }

    /**
     *
     * Select and Update Cnx Employees 
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function updateCnxEmployees() {

        Log::info('START CRON updateCnxEmployees '.Carbon::now()->toDateTimeString());

        $data_to_update = CnxEmployees::query()
            -> leftJoin('udw_migration AS udw', 'udw.employee_number', '=', 'ce.employee_number') 
            -> select('ce.employee_number', 'udw.programme_msa', 'udw.msa_client AS msa_client', 'udw.location AS location_name',
                     'udw.job_profile', 'udw.lob', 'udw.supervisor_id', 'udw.supervisor_full_name', 'udw.supervisor_email_id',
                     'udw.REGION AS region', 'udw.JOB_FAMILY AS job_family')
            -> whereRaw('ce.msa_client <> udw.msa_client OR ce.location_name <> udw.location OR ce.job_profile <> udw.job_profile OR ce.lob <> udw.lob
                    OR ce.supervisor_full_name <> udw.supervisor_full_name OR ce.region <> udw.REGION
                    OR ce.job_family <> udw.JOB_FAMILY')
            -> take(500)
            -> get();
        
        $data_to_update->map(function ($cnx) {
             $value = $cnx->only(['employee_number', 'programme_msa', 'msa_client', 'location_name', 'job_profile', 'lob', 'supervisor_id', 'supervisor_full_name', 'supervisor_email_id', 'region', 'job_family']);
            //  echo " UPDATING {$value['employee_number']} " . PHP_EOL;
             $employee_details_array = (array) $value;
             CnxEmployees::where('employee_number', $value['employee_number'] ) ->  update( $employee_details_array );
        });

        Log::info('END CRON updateCnxEmployees '.Carbon::now()->toDateTimeString());
    }

    public function getInvalidUsernames($page = 1 , $conditions = [], $search = "", $per_page = 20) {
        $query = DB::table('cnx_employees_unlisted')
            -> selectRaw('MAX(updated_at) AS date_triggered, username, MAX(subnet) AS subnet, COUNT(username) AS attempt')
            -> groupBy('username');

        if( $this -> sortBy )
            $query -> orderBy( $this -> sortBy, $this -> sortOrder );
        else
            $query -> orderBy('date_triggered', 'desc');

        if($search != "")
            $query ->where('username', 'like', "%$search%");
            
            
        return $query -> paginate($per_page, ['*'], 'page', $page ); 
    }

    public function fetchUnlisted()
    {
        DB::insert("SET sql_mode = ''");
        $query = DB::table('cnx_employees_unlisted')
            -> selectRaw('MAX(updated_at) AS date_triggered, username, MAX(subnet) AS subnet, COUNT(username) AS attempt')
            -> groupBy('username')
            -> orderBy('created_at', 'ASC')
            -> limit(10)
            ->get();

        return $query;

    }

    public function UpdateOrInsertEmployee($employee_id, $details)
    {
        $exist = DB::table('cnx_employees')
                    -> where('employee_number', $employee_id)
                    -> first();
        
        $data = array(
            'firstname'         => $details->input('givenName'),
            'lastname'          => $details->input('sn'),
            'employee_number'   => $details->input('employeeID'),
            'email'             => $details->input('mail'),
            'sam_account_name'  => $details->input('sAMAccountName')
        );

        if($exist)
        {
            if(DB::table('cnx_employees') -> where('employee_number', $employee_id)->update($data))
            {
                if($this - removeFromUnlisted($details->input('sAMAccountName'))){
                    return array('status' => TRUE, 'msg' => 'cnx_employees table is updated','employee_id' => $employee_id);
                }else{
                    return array('status' => FALSE, 'msg' => 'An error occured during removing of username from unlisted table','employee_id' => $employee_id);
                }
            }else{
                return array('status' => FALSE, 'msg' => 'Updating is unsucessful','employee_id' => $employee_id);
            }
        }else{
            if(DB::table('cnx_employees') -> insert($data))
            {
                if($this - removeFromUnlisted($details->input('sAMAccountName'))){
                    return array('status' => TRUE, 'msg' => 'Data was successfully inserted in cnx_employees table','employee_id' => $employee_id);
                }else{
                    return array('status' => FALSE, 'msg' => 'An error occured during removing of username from unlisted table','employee_id' => $employee_id);
                }
            }else{
                return array('status' => FALSE, 'msg' => 'Data was not inserted','employee_id' => $employee_id);
            }
            
        }
    }

    public function removeFromUnlisted($username)
    {
        if(DB::table('cnx_employees_unlisted') -> where('username', $username) -> delete())
        {
            return true;
        }else{
            return false;
        }
    }

}