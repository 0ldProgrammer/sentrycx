<?php

namespace App\Modules\Flags\Services;

use App\Modules\Flags\Helpers\FlagsQueryHelper;
use App\Modules\Flags\Models\Flag;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border;
use stdClass;

class FlagService {

    /** @var Array $var description */
    const FIELDS_MAPPING = [
        'agent_name'    => 'rd.agent_name',
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
        'host_name'     => 'wp.host_name',
        'connection'    => 'ac.connection_type',
        'agent'         => 'wp.net_type as agent',
        'mtr_highest_avg' => 'ac.mtr_highest_avg',
        'mtr_highest_loss' => 'ac.mtr_highest_loss',
        'download_speed'   => 'wp.download_speed',
        'upload_speed' => 'wp.upload_speed',
        'average_latency' => 'wp.average_latency',
        'packet_loss' => 'wp.packet_loss',
        'jitter' => 'wp.jitter',
        'mos' => 'wp.mos',
        'lob' => 'wd.lob',
        'programme_msa' => 'wd.programme_msa',
        'job_profile' => 'wd.job_profile',
        'supervisor_email_id' => 'wd.supervisor_email_id',
        'supervisor_full_name' => 'wd.supervisor_full_name'
    ];

    const HEADERS_SUMMARY_REF = [
        'ref_no' => 'Reference No',
        'agent_name' => 'AgentName',
        'host_name' => 'Username',
        'code' => 'Code',
        'category' => 'Category',
        'connection' => 'Connection',
        'agent' => 'Agent',
        'location' => 'Location',
        'account' => 'Account',
        'status_info' => 'Status',
        'VLAN' => 'VLAN',
        'DNS_1' => 'DNS1',
        'DNS_2' => 'DNS2 ',
        'subnet' => 'Subnet',
        'ISP' => 'ISP',
        'mtr_highest_avg' => 'HighestAVG',
        'mtr_highest_loss' => 'HighestLOSS',
        'download_speed' => 'DOWNSpeed',
        'upload_speed' => 'UPSpeed',
        'average_latency' => 'AVGLAT',
        'packet_loss' => 'APLOSS',
        'jitter' => 'Jitter',
        'mos' => 'MOS',
        'timestamp_submitted'  => 'Timestamp Submitted',
        'date_created' => 'Datetime Issue',
        'audio' => 'Audio',
        'mic' => 'Mic',
        'video' => 'Video',
        'lob' => 'LOB',
        'programme_msa' => 'Programme MSA',
        'job_profile' => 'Position',
        'supervisor_email_id' => 'Supervisor Email',
        'supervisor_full_name' => 'Supervisor'
    ];

    const DATA_SUMMARY_REF = [
        'ref_no' => 'rd.ref_no',
        'agent_name' => 'ac.agent_name',
        'host_name' => 'wp.host_name',
        'code' => 'ol.options',
        'category' => 'cat.name',
        'connection' => 'ac.connection_type',
        'agent' => 'wp.net_type as agent',
        'location' => 'ac.location',
        'account' => 'ac.account',
        'status_info' => 'rd.status_info',
        'VLAN' => 'wp.VLAN',
        'DNS_1' => 'wp.DNS_1',
        'DNS_2' => 'wp.DNS_2 ',
        'subnet' => 'wp.subnet',
        'ISP' => 'wp.ISP',
        'download_speed' => 'wp.download_speed',
        'upload_speed' => 'wp.upload_speed',
        'mtr_highest_avg' => 'ac.mtr_highest_avg',
        'mtr_highest_loss' => 'ac.mtr_highest_loss',
        'average_latency' => 'wp.average_latency',
        'packet_loss' => 'wp.packet_loss',
        'jitter' => 'wp.jitter',
        'mos' => 'wp.mos',
        'timestamp_submitted'  => 'rd.timestamp_submitted',
        'date_created' => 'wp.date_created',
        'audio' => 'amd.audio',
        'mic' => 'amd.mic',
        'video' => 'amd.video',   
        'lob' => 'wd.lob',
        'programme_msa' => 'wd.programme_msa',
        'job_profile' => 'wd.job_profile',
        'supervisor_email_id' => 'wd.supervisor_email_id',
        'supervisor_full_name' => 'wd.supervisor_full_name'
    ];

    /** @var Array $var description */
    const SINGLE_FIELDS = ['agent_name'];

    /** @var String $sortBy */
    protected $sortBy = null;
    protected $sortOrder = 'asc';

    public function setSort($field, $order){
        $this -> sortBy = $field;
        $this -> sortOrder =  strtolower( $order );
    }

    

    /** @var Flag $flag description */
    protected $flag = null;
    /**
     * Constructor Dependencies
     *
     * @param Flag $flag
     * @return type
     * @throws conditon
     **/
    public function __construct(Flag $flag)
    {
        $this -> flag = $flag;
    }



    /**
     *
     * Check the count of the
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function getFlagCount($account, $category_id ){
        // $query -> leftJoin('options_list as ol','rd.code_id', '=', 'ol.id');
        return DB::table('redflag_dashboard as rd')
            -> leftJoin('options_list as ol', 'rd.code_id', '=', 'ol.id')
            -> where('rd.status_info', '<>', 'Closed')
            -> where('rd.account', $account )
            -> where('ol.category_id', $category_id)
            -> count();
    }


    /**
     *
     * Setup extra query based on overview filters
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function _overviewFilters($filters){
        $condition_string = '';
        $conditions = [];
        foreach( $filters as $filter => $values ) {
            if( !$values )
                continue;

            $include_values = implode('", "', $values );
            $conditions[] = " {$filter} IN (\"{$include_values}\") ";
        }

        if( $conditions )
            $condition_string = " AND " . implode( " AND ", $conditions );

        return $condition_string;
    }

    /**
     * Retrieve the summary of the flags
     *
     * @param
     * @return Array
     * @throws conditon
     **/
    public function accountOverview( $filters = [], $breakdown = '' , \StdClass $user) {
        $condition_string = FlagsQueryHelper::conditions($filters, $user );
        $total_conditions = $this -> _overviewFilters( $filters );

        DB::insert("SET sql_mode = ''");
        
        $sql_string = FlagsQueryHelper::overview( $condition_string, $total_conditions );

        if( $breakdown )
            $sql_string = FlagsQueryHelper::breakdown($condition_string, $breakdown);

        #die( $sql_string );

        return DB::select( DB::raw( $sql_string ));
    }

    /**
     * Breakdown per country on summary 
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function totalCount($filters = [], \stdClass $user){
        $condition_string = FlagsQueryHelper::conditions( $filters, $user );
        $total_conditions = $this -> _overviewFilters( $filters );

        DB::insert("SET sql_mode = ''");

        $sql_string = FlagsQueryHelper::total( $condition_string, $total_conditions);


        return DB::select( DB::raw( $sql_string ));
    }



    /**
     *
     * Retrieve all the filters
     *
     * @param Array $fields
     * @return type
     * @throws conditon
     **/
    public function getFilters($fields, $table = 'redflag_dashboard')
    {
        $filters = [];

        foreach( $fields as $field ) {
            $sql_string = "SELECT DISTINCT $field FROM $table WHERE $field IS NOT NULL ORDER BY $field ASC ";

            $values = DB::select( DB::raw( $sql_string ) );

            $filters[ $field ] = collect( $values ) -> pluck( $field ) -> all();
        }

        return $filters;
    }

    /**
     * Query the flags needed
     * TODO : Try to refractor this , at least break into multiple functions
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function query(stdClass $user , $page = 1, $per_page = 20, $conditions = [] ){
        $field_mapping = self::FIELDS_MAPPING;

        $query = DB::table('redflag_dashboard as rd');

        $query -> leftJoin('options_list as ol','rd.code_id', '=', 'ol.id');
        $query -> leftJoin('category AS cat', 'ol.category_id', '=', 'cat.id');
        $query -> leftJoin('cnx_employees as wd', 'wd.employee_number', '=', 'rd.worker_id' );

        $query -> leftJoin('agent_media_device as amd', 'amd.worker_id', '=', 'rd.worker_id' );

        $query -> leftJoin('workstation_profile AS wp', function( $join ){
            $join -> on( 'wp.redflag_id', '=', 'rd.id')
            ->on('wp.id', '=', DB::raw("(SELECT max(id) from workstation_profile WHERE workstation_profile.redflag_id = rd.id)"));
        });

        $query -> leftJoin('agent_connections AS ac', function( $join ){
            $join -> on( 'ac.worker_id', '=', 'rd.worker_id')
                ->on('ac.id', '=', DB::raw("(SELECT max(id) from agent_connections WHERE agent_connections.worker_id = rd.worker_id )"));
        });

        // TODO : Refractor this by setting this ac private static property
        $query -> addSelect(['rd.*']);
        $query -> addSelect("rd.id AS id");
        $query -> addSelect('wp.id AS station_id');
        $query -> addSelect("cat.name AS category_name");
        $query -> addSelect("ol.options as code_name");
        $query -> addSelect('wp.mtr','wp.host_ip_address', 'wp.download_speed', 'wp.upload_speed', 'wp.average_latency', 'wp.packet_loss', 'wp.jitter');
        $query -> addSelect('wp.date_created','wp.station_number','wp.subnet','wp.gateway', 
            'wp.DNS_1','wp.DNS_2','wp.VLAN','wp.ISP',
            'wp.download_speed','wp.upload_speed', 
            'wp.host_name', 'wp.isp_fullname', 'wp.desktop_app_version', 'wp.net_type as agent');
        $query -> addSelect('ac.is_active', 'ac.mtr_highest_avg', 'ac.mtr_highest_loss');
        $query -> addSelect('amd.audio', 'amd.mic', 'amd.video');
        $query -> addSelect('ac.id AS connection_id');
        $query -> addSelect('ac.has_threshold AS has_threshold');
        $query -> addSelect('ac.connection_type');
        $query -> addSelect('wp.mos as mos');
        $query -> addSelect('wd.employee_number','wd.email','wd.supervisor_email_id',
        'wd.supervisor_full_name','wd.country','wd.job_profile', 'wd.lob',
        'wd.location_name','wd.msa_client','wd.programme_msa');
        $query -> where('rd.status_info', '<>', 'Closed');

        foreach( $conditions as $field => $value  ){

            if( $field == 'connection' ) {
                if( $value == 'offline' )
                    $query -> where( function($q){
                        $q -> where('ac.is_active', NULL );
                        $q -> orWhere('ac.is_active', FALSE);
                    });
                else if( $value == 'online' )
                    $query -> where('ac.is_active', TRUE);
                continue;
            }
            

            if( $field == 'old_unresolved') {
                
                // if( $value ) continue;
                $checker = ( $value ) ? '<' : '>';

                $timestamp_check = Carbon::now() -> subHours( getenv('OLD_UNRESOLVE_TIME'));

                $query -> where('rd.timestamp_submitted', $checker , $timestamp_check );

                if( !$value )
                    $query -> where('ac.is_active', TRUE);

                continue;
            }

            if( in_array($field, self::SINGLE_FIELDS ) ){
                $field_value = implode('',$value);
                $query -> where( $field_mapping[$field], 'like', "%{$field_value}%");
                continue;
            }

            if( is_array($value) ){
                $query -> whereIn( $field_mapping[$field], $value );
                continue;
            }
            $query -> where( $field_mapping[$field], $value );
        }


        if( $user -> location ){
            
            $location_list   =  explode(",", $user ->location );

            $access_location = implode('","', $location_list );

            $query -> whereRaw(" ac.location IN ( SELECT DISTINCT tmp.account FROM redflag_dashboard tmp WHERE tmp.location IN (\"{$access_location}\") )" );
        }

        if( $user -> account_access ){
            $account_list   =  explode(",", $user ->account_access );

            $access_account = implode('","', $account_list );

            $query -> whereRaw(" ac.account IN ( SELECT DISTINCT tmp.account FROM redflag_dashboard tmp WHERE tmp.account IN (\"{$access_account}\") )" );
        }

        // if( !array_key_exists( 'connection', $conditions )){
        //     $query -> where('ac.is_active', TRUE);
        //     $query -> where('ac.is_admin', FALSE);
        // }

        

        if( $this -> sortBy )
            $query -> orderBy( $field_mapping[ $this -> sortBy], $this -> sortOrder );
        else
            $query -> orderBy('rd.id', 'desc');


        return $query -> paginate($per_page, ['*'], 'page', $page );
    }

    /**
     * Retrieve the specific issue by ID
     *
     * @param int $id
     * @return Collection
     * @throws conditon
     **/
    public function updateStatus( $id, $status )
    {
        $date_now = date("Y-m-d H:i:s");
        $update_data = ['status_info' => $status ];

        switch( $status ) {
            case 'Acknowledge' :
                $update_data['timestamp_acknowledged'] = $date_now;
                break;
            case 'Closed' :
                $udpate_data['timestamp_closed'] = $date_now;
            case 'Resolve' :
            default;
                break;
        }

        $query = DB::table('redflag_dashboard');
        if( is_array( $id ))
            $query -> whereIn('id', $id);
        else
            $query -> where('id', $id);

        return $query -> update( $update_data );
    }

    /**
     *
     * Update the status of the flags by criteria
     *
     * @param String $status
     * @param Array $criteria
     * @return type
     * @throws conditon
     **/
    public function updateStatusMultiple($status = 'Acknowledge', $criteria ){
        $field_mapping = self::FIELDS_MAPPING;

        $query  = DB::table('redflag_dashboard AS rd')
            -> leftJoin('workstation_profile AS wp', 'rd.id', '=', 'wp.redflag_id')
            -> leftJoin('options_list as ol', 'rd.code_id', '=', 'ol.id')
            -> leftJoin('agent_connections as ac', 'ac.worker_id', '=', 'rd.worker_id')
            -> leftJoin('category as cat', 'cat.id', '=', 'ol.category_id');

        foreach( $criteria as $field_key => $value ) {

            $field_key = str_replace('[]', '',$field_key);

            if ($field_key == 'old_unresolved') {
                
                $checker = ( $value ) ? '<' : '>';

                $timestamp_check = Carbon::now() -> subHours( getenv('OLD_UNRESOLVE_TIME'));

                $query -> where('rd.timestamp_submitted', $checker , $timestamp_check );

                if( !$value ) {
                    $query -> where('ac.is_active', TRUE);
                }

                continue;
            }

            $field_name = $field_mapping[ $field_key ];
            if( !$value ) continue;
    
            if( in_array($field_key, self::SINGLE_FIELDS ) ){
                $query -> where( $field_name, 'like', "%{$value}%");
                continue;
            }

            if( is_array($value) ){
                $query -> whereIn( $field_name, $value ) ;
                continue;
            }

            $query -> where( $field_name, $value );
            
        }



        if( $status == 'For Confirmation')
            $query -> where('status_info', 'Acknowledge');

        else if ( $status == 'Acknowledge')
            $query -> where('status_info', 'Inquiry');

        $ids = $query -> get(['rd.id']) -> pluck('id') -> toArray();

        return $this -> updateStatus($ids, $status );
    }



    /**
     * Saves the flag Record
     *
     * @param Array $flag_details
     * @return type
     * @throws conditon
     **/
    public function save( $flag_details )
    {


        // $this -> flag -> category_id = $flag_details['category_id'];
        // $this -> flag -> code_id = $flag_details['code_id'];
        // $this -> flag -> employee_id = $flag_details['employee_id'];
        // $this -> flag -> country = $flag_details['country'];
        // $this -> flag -> account = $flag_details['account'];
        // $this -> flag -> isp = $flag_details['isp'];
        // $this -> flag -> location = $flag_details['location'];
        // $this -> flag -> gateway = $flag_details['gateway'];
        // $this -> flag -> vlan    = $flag_details['vlan'];
        // $this -> flag -> dns_1   = $flag_details['dns_1'];
        // $this -> flag -> dns_2   = $flag_details['dns_2'];
        // $this -> flag -> station_id = $flag_details['station_id'];
        // $this -> flag -> status  = $flag_details['status'];
        // $this -> flag -> status  = "OPEN";

        // $this -> flag -> save();

        // return $this -> flag;
    }

    public function generateExcelPdfReportFromSummary($report_type, stdClass $user, $conditions = [], $headers, $timezone) {
        $field_mapping = self::FIELDS_MAPPING;
        
        $include_agent_name = array('agent_name');
        $new_data_array = array();
        $list_of_headers = array();
        $list_of_data_selected = array();
        $fetch_exist = array();
        $all_headers = [
            'ref_no', 'agent_name', 'host_name', 'code', 'category', 'connection', 
            'agent', 'VLAN', 'DNS_1', 'DNS_2', 'subnet', 'ISP', 'location', 'account',  
            'status_info', 'mtr_highest_avg', 'mtr_highest_loss', 'download_speed',
            'upload_speed', 'average_latency', 'packet_loss', 'jitter', 'mos', 
            'timestamp_submitted', 'date_created', 'media_device', 'lob', 'programme_msa',
            'job_profile', 'supervisor_email_id', 'supervisor_full_name'
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

        foreach($fetch_exist as $header) {
            $header_name = self::HEADERS_SUMMARY_REF[$header]; 
            $data_name = self::DATA_SUMMARY_REF[$header]; 
            array_push($list_of_headers, $header_name);
            array_push($list_of_data_selected, $data_name);
        }
        
        $data_selected = implode(',',$list_of_data_selected);

        $query = DB::table('redflag_dashboard as rd');

        $query -> leftJoin('options_list as ol','rd.code_id', '=', 'ol.id');
        $query -> leftJoin('category AS cat', 'ol.category_id', '=', 'cat.id');
        $query -> leftJoin('cnx_employees as wd', 'wd.employee_number', '=', 'rd.worker_id' );

        $query -> leftJoin('agent_media_device as amd', 'amd.worker_id', '=', 'rd.worker_id' );

        $query -> leftJoin('workstation_profile AS wp', function( $join ){
            $join -> on( 'wp.redflag_id', '=', 'rd.id')
            ->on('wp.id', '=', DB::raw("(SELECT max(id) from workstation_profile WHERE workstation_profile.redflag_id = rd.id)"));
        });

        $query -> leftJoin('agent_connections AS ac', function( $join ){
            $join -> on( 'ac.worker_id', '=', 'rd.worker_id')
                ->on('ac.id', '=', DB::raw("(SELECT max(id) from agent_connections WHERE agent_connections.worker_id = rd.worker_id )"));
        });

        $query -> selectRaw($data_selected);

        // TODO : Refractor this by setting this ac private static property

        $query -> where('rd.status_info', '<>', 'Closed');

        foreach( $conditions as $field => $value  ){

            if( $field == 'connection' ) {
                if( $value == 'offline' )
                    $query -> where( function($q){
                        $q -> where('ac.is_active', NULL );
                        $q -> orWhere('ac.is_active', FALSE);
                    });
                else if( $value == 'online' )
                    $query -> where('ac.is_active', TRUE);
                continue;
            }
            

            if( $field == 'old_unresolved') {
                // if( $value ) continue;
                $checker = ( $value ) ? '<' : '>';

                $timestamp_check = Carbon::now() -> subHours( getenv('OLD_UNRESOLVE_TIME'));

                $query -> where('rd.timestamp_submitted', $checker , $timestamp_check );

                if( !$value )
                    $query -> where('ac.is_active', TRUE);

                continue;
            }

            if( in_array($field, self::SINGLE_FIELDS ) ){
                $field_value = implode('',$value);
                $query -> where( $field_mapping[$field], 'like', "%{$field_value}%");
                continue;
            }

            if( is_array($value) ){
                $query -> whereIn( $field_mapping[$field], $value );
                continue;
            }
            $query -> where( $field_mapping[$field], $value );
        }


        if( $user -> location ){
            
            $location_list   =  explode(",", $user ->location );

            $access_location = implode('","', $location_list );

            $query -> whereRaw(" ac.location IN ( SELECT DISTINCT tmp.account FROM redflag_dashboard tmp WHERE tmp.location IN (\"{$access_location}\") )" );
        }

        if( $user -> account_access ){
            $account_list   =  explode(",", $user ->account_access );

            $access_account = implode('","', $account_list );

            $query -> whereRaw(" ac.account IN ( SELECT DISTINCT tmp.account FROM redflag_dashboard tmp WHERE tmp.account IN (\"{$access_account}\") )" );
        }    

        if( $this -> sortBy ) {
            $query -> orderBy( $field_mapping[ $this -> sortBy], $this -> sortOrder );
        } else {
            $query -> orderBy('rd.id', 'desc');
        }

        $data = $query -> get();
    
        $data_array = json_decode(json_encode($data), true);
        
        foreach($data_array as $value){
            if(isset($value['date_created'])){
                $get_current_date = Carbon::createFromTimestamp(strtotime($value['date_created']))
                    ->timezone($timezone)
                    ->toDateTimeString();
    
                $value['date_created'] = Carbon::parse($get_current_date)->format('Y-m-d h:i:s a');
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
            foreach($new_data_array as $value) {
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
            foreach($new_data_array as $value) {
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
            $writer -> save('php://output');
        }
            
    }

    private function CheckZeroes($input)
    {
        return preg_match('/^[0]*$/', $input);
    }


}
