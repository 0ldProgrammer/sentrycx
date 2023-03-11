<?php
namespace App\Modules\Flags\Helpers;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class FlagsQueryHelper {

    /**
     * Generates conditional statement
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public static function conditions($filters, $user ){
        $condition_string = "";
        $conditions = [];


        $field_mapping = [
            'account'  => 'flags.account',
            'location' => 'flags.location',
            'country'  => 'flags.country'
        ];

        $conditions[] = "flags.status_info <> 'Closed' ";
        $conditions[] = "main_ac.is_active = TRUE";
        $conditions[] = "main_ac.is_admin = FALSE";

        $timestamp_check = Carbon::now() -> subHours( getenv('OLD_UNRESOLVE_TIME'))->format("Y-m-d H:i:s");
        // $query -> where('rd.timestamp_submitted', $checker , $timestamp_check );
        $conditions[] = "flags.timestamp_submitted > '$timestamp_check'";

        foreach( $filters as $filter => $values ) {
            if( !$values )
                continue;

            $include_values = implode('", "', $values );
            $conditions[] = " {$field_mapping[$filter]} IN (\"{$include_values}\") ";
        }

        if( $user -> location && !$user -> account_access){

            $location_list   =  explode(",", $user ->location );

            $access_location = implode('","', $location_list );

            $conditions[] = "flags.account IN ( SELECT DISTINCT tmp.account FROM redflag_dashboard tmp WHERE tmp.location IN (\"{$access_location}\")  ) ";
        }

        if( $user -> account_access ){
            
            $account_list   =  explode(",", $user ->account_access );

            $access_account = implode('","', $account_list );

            $conditions[] = "flags.account IN ( SELECT DISTINCT tmp.account FROM redflag_dashboard tmp WHERE tmp.account IN (\"{$access_account}\")  ) ";
        }

        // $timestamp_check = Carbon::now() -> subHours( getenv('OLD_UNRESOLVE_TIME')) -> toDateTimeString();
        // $conditions[] = "flags.timestamp_submitted > '$timestamp_check'";

        if( $conditions )
            $condition_string = " WHERE " . implode( " AND ", $conditions );

        

        return $condition_string;
    }


    /**
     * Generates Raw SQL Query Strings for retrieving the overview
     *
     * @param String  $condition_string
     * @return type
     * @throws conditon
     **/
    public static function overview($condition_string = "", $total_conditions = "" ){
        $query_string = "
            SELECT 
                name, 
                rd.account, 
                COUNT( worker_id ) alert_count, 
                ac.login_count, 
                url,
                COALESCE(( (COUNT( worker_id )/ ac.login_count) * 100),0 ) AS percentage 
            FROM
            (
                SELECT DISTINCT fc.name, flags.account, flags.worker_id, pb.url
                FROM redflag_dashboard  flags
                LEFT JOIN playbook AS pb ON flags.account = pb.client 
                LEFT JOIN options_list AS opt ON flags.code_id = opt.id 
                LEFT JOIN category AS fc ON fc.id = opt.category_id 
                LEFT JOIN agent_connections as main_ac ON main_ac.worker_id = flags.worker_id
                $condition_string
            ) rd 

            LEFT JOIN (
                SELECT 
                    DISTINCT account, 
                    COUNT(login.id) as login_count 
                FROM agent_connections login
                LEFT JOIN workstation_profile AS wp ON wp.worker_id=login.worker_id
                WHERE 
                    account IN ( SELECT DISTINCT account FROM redflag_dashboard  ) 
                AND is_active=TRUE AND is_admin=FALSE AND redflag_id=0
                $total_conditions
                GROUP BY account 
            ) ac ON ac.account=rd.account
            
            GROUP BY rd.name, rd.account
        ";
    
        return $query_string;
    }

    /**
     * TODO : This is almost the same with overview in terms of SQL
     * Generates Raw SQL Query Strings for breaking down
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public static function breakdown($condition_string = '', $breakdown = 'country'){
        $query_string = "
            SELECT 
                name, 
                rd.account, 
                rd.{$breakdown},
                COUNT( worker_id ) alert_count, 
                ac.login_count, 
                url,
                COALESCE(( (COUNT( worker_id )/ ac.login_count) * 100),0 ) AS percentage 
            FROM
            (
                SELECT DISTINCT fc.name, flags.{$breakdown}, flags.account, flags.worker_id, pb.url
                FROM redflag_dashboard  flags
                LEFT JOIN playbook AS pb ON flags.account = pb.client 
                LEFT JOIN options_list AS opt ON flags.code_id = opt.id 
                LEFT JOIN category AS fc ON fc.id = opt.category_id 
                LEFT JOIN agent_connections as main_ac ON main_ac.worker_id = flags.worker_id
                $condition_string
            ) rd 

            LEFT JOIN (
                SELECT 
                    DISTINCT account, $breakdown,
                    COUNT(login.id) as login_count 
                FROM agent_connections login
                LEFT JOIN workstation_profile AS wp ON wp.worker_id=login.worker_id
                WHERE 
                account IN ( SELECT DISTINCT account FROM redflag_dashboard ) 
                AND is_active=TRUE AND is_admin=FALSE AND redflag_id=0
                GROUP BY account, $breakdown 
            ) ac ON ac.account=rd.account AND ac.$breakdown = rd.$breakdown
            
            GROUP BY rd.name, rd.account, rd.{$breakdown}
        ";
    
        return $query_string;
    }

    /**
     * TODO : This is almost the same with overview in terms of SQL
     * Generates Raw SQL Query Strings for total count
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public static function total($condition_string = '', $total_conditions = '' ){
        $query_string = "
            SELECT 
                'TOTAL' as account,
                name,
                COUNT( worker_id ) alert_count, 
                rd.login_count,
                COALESCE(( (COUNT( worker_id )/ rd.login_count) * 100),0 ) AS percentage 
            FROM
            (
                SELECT DISTINCT fc.name,  flags.account, flags.worker_id, pb.url,
                (
                    SELECT 
                    COUNT(login.id) as login_count 
                    FROM agent_connections login
                    LEFT JOIN workstation_profile AS wp ON wp.worker_id=login.worker_id
                    WHERE 
                    account IN ( SELECT DISTINCT account FROM redflag_dashboard ) 
                    AND is_active=TRUE AND is_admin=FALSE AND redflag_id=0
                    $total_conditions
                ) as login_count
                FROM redflag_dashboard  flags
                LEFT JOIN playbook AS pb ON flags.account = pb.client 
                LEFT JOIN options_list AS opt ON flags.code_id = opt.id 
                LEFT JOIN category AS fc ON fc.id = opt.category_id 
                LEFT JOIN agent_connections as main_ac ON main_ac.worker_id = flags.worker_id
                $condition_string
            ) rd 
            GROUP by rd.name
        ";
    
        return $query_string;
    }

}
