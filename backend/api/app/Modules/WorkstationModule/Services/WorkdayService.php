<?php

namespace App\Modules\WorkstationModule\Services;
use Illuminate\Support\Facades\DB;

class WorkdayService {

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
    public function __construct(){
		$this->DB_READ = config('dbreadwrite.db_read');
    }


    /**
     *
     * Undocumented function long description
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function getEmployeeInfo( $worker_id ){
        return DB::table('cnx_employee') -> where('employee_number', $worker_id );
    }

    /**
     * Retrieve the Workday Profile
     *
     * @return type
     * @throws conditon
     **/
    public function getWorkdayProfile($username)
    {
        $email = $username."@concentrix.com";
        // return $results = DB::table('cnx_employees')->where('email', $email)->get();
        $query = DB::connection($this->DB_READ)->table('cnx_employees as emp')->leftJoin('users', 'emp.email', '=', 'users.email');
        $query -> addSelect("emp.employee_number");
        $query -> addSelect("emp.firstname");
        $query -> addSelect("emp.lastname");
        $query -> addSelect("emp.email");
        $query -> addSelect("emp.location_name");
        $query -> addSelect("emp.job_profile");
        $query -> addSelect("emp.programme_msa");
        $query -> addSelect("emp.supervisor_id");
        $query -> addSelect("emp.supervisor_email_id");
        $query -> addSelect("emp.supervisor_full_name");
        $query -> addSelect("emp.msa_client");
        $query -> addSelect("emp.lob");
        $query -> addSelect("emp.country");
        $query -> addSelect("users.id AS isAdmin");
        return $query -> where('emp.email', $email)->get();
    }
}
