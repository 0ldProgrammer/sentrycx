<?php

namespace App\Modules\Zoho\Services;


use Illuminate\Support\Facades\DB;

class ZohoLogService {


    /**
     *
     * Initialize ZOHO report by saving an entry with session_id, agent and worker_id
     *
     * @param String $worker_id
     * @param String $session_id
     * @return type
     * @throws conditon
     **/
    public function initReport( $worker_id, $session_id ){
        $data = [
            'worker_id'  => $worker_id,
            'session_id' => $session_id
        ];
        return DB::table('zoho_reports')->insert( $data );
    }

    /**
     *
     * Update Zoho Report
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function updateReport($session_id, $args ){
        return DB::table('zoho_reports')
            -> where('session_id', $session_id )
            -> update( $args );
    }

    /**
     *
     * Retreive all logs by workerID
     *
     * @param String $workerID
     * @return type
     * @throws conditon
     **/
    public function getByWorkerID( $worker_id ){
        return DB::table('zoho_reports') -> where('worker_id', $worker_id )-> get();
    }

}
