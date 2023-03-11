<?php 

namespace App\Modules\Reports\Services;

use App\Modules\Reports\Models\AgentConnectionLog;
use Illuminate\Support\Facades\DB;

class ConnectionReportService {

    /**
     *
     * Generate the 30 minutes interval detail
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function query($date_start, $date_end){
        // TODO : Try to change this into Eloquent
        //      : For now use the raw sql for better date range filtering
        $sql_string = " SELECT 
                created_at as logged_time,
                agent_name, 
                agent_email, 
                worker_id, 
                station_name, 
                location, 
                account, 
                country, 
                connection_type, 
                net_type, 
                job_profile, 
                lob, 
                msa_client, 
                programme_msa, 
                supervisor_email_id, 
                supervisor_full_name 
            FROM agent_connections_log WHERE created_at > '$date_start' AND created_at < '$date_end' AND is_admin = FALSE ";
        return DB::select( DB::raw( $sql_string ) );
    }

    /**
     *
     * Generate 30 minutes interval summary
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function summary($date_start, $date_end){
        $query = DB::table('agent_connections_log as ac');
        $query -> addSelect('created_at as logged_time');
        $query -> addselect(DB::raw("COUNT(account) as connected" ) );
        $query -> addSelect(DB::raw("SUM( CASE WHEN ac.connection_type = 'WIRED' THEN 1 ELSE 0 END) as wired" ) );
        $query -> addSelect(DB::raw("SUM( CASE WHEN ac.connection_type = 'WIRELESS' THEN 1 ELSE 0 END) as wireless" ) );
        $query -> addSelect(DB::raw("SUM( CASE WHEN ac.net_type = 'WAH' THEN 1 ELSE 0 END) as wah" ) );
        $query -> addSelect(DB::raw("SUM( CASE WHEN ac.net_type = 'B&M' THEN 1 ELSE 0 END) as bm" ) );
        $query -> addSelect(DB::raw("SUM( CASE WHEN ac.net_type = 'VPN' THEN 1 ELSE 0 END) as vpn" ) );

        $query -> whereRaw( DB::raw("created_at > '$date_start' AND created_at < '$date_end' "));
        $query -> where('is_admin', FALSE);
        $query -> groupBy('created_at');

        return $query -> get();
        
    }
}