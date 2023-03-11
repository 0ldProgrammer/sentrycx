<?php

namespace App\Modules\Maintenance\Services;
use Illuminate\Support\Facades\DB;

class UserService {

    /** @var String $sortBy */
    protected $sortBy = null;
    protected $sortOrder = 'asc';
    
    public function setSort($field, $order){
        $this -> sortBy = $field;
        $this -> sortOrder =  strtolower( $order );
    }

    /**
     *
     * Retreive the user details by userID
     *
     * @param Integer $id
     * @return type
     * @throws conditon
     **/
    public function get( $id ){
        return DB::table('users')
            -> where('id', $id)
            -> get()[0];
    }

    /**
     *
     * Retrieve the list of users
     *
     * @param Int $page
     * @param Int $per_page
     * @param Array $conditions
     * @return type
     * @throws conditon
     **/
    public function query( $page = 1, $per_page = 20, $conditions = [] ){
        $query = DB::table('users');
        foreach( $conditions as $field => $value  ){
            
            foreach($value as $exact_value){
               $query -> where( $field, 'like', "%$exact_value%" );
            }
        }
        return $query -> paginate( $per_page, ['*'], 'page', $page );
    }

    /**
     *
     * fetch list of programme msa in automated_users table
     *
     * @param Int $page
     * @param Int $per_page
     * @param Array $conditions
     * @return type
     * @throws conditon
     **/
    public function msaQuery( $page = 1, $per_page = 20, $conditions = [] ){
        $query = DB::table('automated_users');
        if ($conditions) {
            foreach( $conditions as $field => $value  ){
                
                foreach($value as $exact_value){
                $query -> where( $field, 'like', "%$exact_value%" );
                }
            }
        }
        return $query -> paginate( $per_page, ['*'], 'page', $page );
    }

    /**
     *
     * Use to add/edit user
     *
     * @param Array $data
     * @param Int $id
     * @return type
     * @throws conditon
     **/
    public function save( $data, $id = 0 ){
        $current_time = date("Y-m-d H:i:s");

        $data['location'] = $this -> _arrayEncode( $data['location'] );
        $data['scope_access'] = $this -> _arrayEncode( $data['scope_access']);
        $data['account_access'] = $this -> _arrayEncode( $data['account_access']);

        if( !$id )
            $data['created_at'] = $current_time;

        $data['updated_at'] = $current_time;

        return DB::table('users') -> updateOrInsert(['id' => $id], $data );
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

    /**
     *
     * Remove the user
     *
     * @param Int $id
     * @return type
     * @throws conditon
     **/
    public function delete($id){
        return DB::table('users') -> delete( $id );
    }

    /**
     *
     * Fetch users based on search
     *
     * @param Int $id
     * @return type
     * @throws conditon
     **/
    public function getUsers($page = 1 , $conditions = [], $search = "", $per_page = 20)
    {
        
        $query = DB::table('users');

        if( $this -> sortBy )
            $query -> orderBy( $this -> sortBy, $this -> sortOrder );
        else
            $query -> orderBy('id', 'asc');

        if($search != "")
            $query ->where('username', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
        
        return $query -> paginate($per_page, ['*'], 'page', $search ? 1 : $page  ); 
    }

    /**
     *
     * Retrieve all the filters
     *
     * @param Array $fields
     * @return type
     * @throws conditon
     **/
    public function getFilters($fields, $table = "users")
    {
        $filters = [];
        
        foreach( $fields as $field ) {

            $sql_string = "SELECT DISTINCT $field FROM $table WHERE $field IS NOT NULL ORDER BY $field ASC ";

            $values = DB::select( DB::raw( $sql_string ) );
            
            $temp_array = collect( $values ) -> pluck( $field ) -> all();

            $convert_to_string = implode(",", $temp_array);
            $final_array = explode(",", $convert_to_string);
           
            $filters[$field] = $final_array;
        
        }
    
        return $filters;
    }

    /**
     * Retrieve the list of Programme MSA
     *
     * @return type
     * @throws conditon
     **/
    public function fetchMSA()
    {
        return DB::table('cnx_employees')
        -> select('cnx_employees.programme_msa', 'automated_users.tagging')
        -> distinct()
        -> leftJoin('automated_users', 'cnx_employees.programme_msa', '=', 'automated_users.programme_msa' )
        -> where('cnx_employees.programme_msa', '<>', null)
        -> orderBy('cnx_employees.programme_msa', 'ASC')
        -> get();
    }
    
    /**
     *
     * Retrieve msa data
     *
     * @return type
     * @throws conditon
     **/
    public function getProgrammeMSA($page = 1 , $conditions = [], $search = "", $per_page = 20){

        $query = DB::table('automated_users');

        if( $this -> sortBy )
            $query -> orderBy( $this -> sortBy, $this -> sortOrder );
        else
            $query -> orderBy('id', 'asc');

        if($search != "")
            $query ->where('programme_msa', 'like', "%$search%");
        
        return $query -> paginate($per_page, ['*'], 'page', $page );
    }

    /**
     *
     * Insert new users
     *
     * @return type
     * @throws conditon
     **/
    public function saveMSAUsers($tagging, $programme_msa) {
        $current_date = date('Y-m-d H:i:s');
        $data = array();

        $existing_date = DB::table('automated_users')
            ->select('created_at')
            ->where('programme_msa', '=', $programme_msa)
            ->first();

        $count = DB::table('cnx_employees')
            ->where('programme_msa', '=', $programme_msa)
            ->count();

        if ($existing_date === null) {
            $data['created_at'] = $current_date;
        }

        $data['updated_at'] = $current_date;
        $data['tagging'] =  $tagging;
        $data['count_users'] = $count;

        $check_existing_data = ['programme_msa' => $programme_msa];
        DB::table('automated_users')->updateOrInsert($check_existing_data, $data);
    }

    // insert new users based on programme_msa using cron file NewUserCommand
    public function insertUsersBasedOnMsa() {
        $current_date = date('Y-m-d H:i:s');
        $insert_data = [];

        $included_msa = DB::table('automated_users')
        ->where('tagging', '=', 'included')    
        ->get();

        if (count($included_msa)) {

            $filtered_msa = json_decode($included_msa, true);

            foreach($filtered_msa as $msa_value) {

                $cnx_employees = DB::table('cnx_employees')
                    ->where('programme_msa', '=', $msa_value['programme_msa'])
                    ->get();
                
                $count_users = count($cnx_employees);

                DB::table('automated_users')
                    ->where('programme_msa', '=', $msa_value['programme_msa'])
                    ->update(['count_users' => $count_users]);

                $filtered_users = json_decode($cnx_employees, true);

                foreach($filtered_users as $value) {
                    $firstname = null;
                    $lastname = null;
                    $username = null;
                    $fullname = null;
                    $email = null;

                    if ($value['email']) {
                        $email = $value['email'];
                    } else {
                        $email = '';
                    }
                    
                    if ($value['firstname'] && $value['lastname']) {
                        $initial_firstname = strtok($value['firstname'], " ");
                        $initial_lastname = strtok($value['lastname'], " ");
                        $firstname = strtolower($initial_firstname);
                        $lastname = strtolower($initial_lastname);
                        $username = $firstname. '.' .$lastname;
                        $fullname = $value['firstname'] . ' ' . $value['lastname'];
                    } else {
                        if ($value['firstname']) {
                            $initial_firstname = strtok($value['firstname'], " ");
                            $firstname = strtolower($initial_firstname);
                            $username = $firstname;
                            $fullname = $value['firstname'];
                        } else {
                            $initial_lastname = strtok($value['lastname'], " ");
                            $lastname = strtolower($initial_lastname);
                            $username = $lastname;
                            $fullname = $value['lastname'];
                        }
                    }

                    $user = DB::table('users')
                        ->where('users.firstname', '=', $fullname)
                        ->where('users.email', '=', $email)
                        ->where('users.location', '=', $value['location_name'])
                        ->first();

                    if ($user === null) {

                        $insert_data[] = [
                            'username' => utf8_encode($username),
                            'firstname' => $fullname,
                            'email' => $email,
                            'location' => $value['location_name'],
                            'account_access' => $value['msa_client'],
                            'created_at' => $current_date,
                            'updated_at' => $current_date
                        ];
                    }
                }
            }

            DB::table('users')->insert($insert_data);
        } 


    }

    // removeUsersBasedOnMsa
    public function removeUsersBasedOnMsa() {
        $current_date = date('Y-m-d H:i:s');
        $remove_data = [];

        $excluded_msa = DB::table('automated_users')
        ->where('tagging', '=', 'excluded')    
        ->get();

        if (count($excluded_msa)) {

            $filtered_msa = json_decode($excluded_msa, true);

            foreach($filtered_msa as $msa_value) {

                $cnx_employees = DB::table('cnx_employees')
                    ->where('programme_msa', '=', $msa_value['programme_msa'])
                    ->get();

                $filtered_users = json_decode($cnx_employees, true);

                foreach($filtered_users as $value) {

                    $user = DB::table('users')
                        ->where('users.email', '=', $value['email'])
                        ->first();

                    if ($user) {
                        $remove_data[] = [
                            'email' =>  $value['email']
                        ];
                    }
                }
            }

            DB::table('users')->whereIn('email', $remove_data)->delete();
            DB::table('automated_users')
                ->where('tagging', '=', 'excluded')    
                ->delete();
        }
    }

    /**
      *
     * Remove MSA
     *
     * @param Int $id
     * @return type
     * @throws conditon
     **/
    public function deleteMSA($id){
        return DB::table('automated_users') -> delete( $id );
    }
}
