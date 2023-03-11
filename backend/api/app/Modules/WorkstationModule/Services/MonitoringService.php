<?php

namespace App\Modules\WorkstationModule\Services;

use Illuminate\Support\Facades\DB;

/**
 * Application Service
 */
class MonitoringService {

    /**
     *
     * Save Monitoring details
     *
     * @param String $worker_id
     * @param Array $args
     * @return
     * @throws conditon
     **/
    public function save($worker_id, $args ){
        $timestamp_submitted = date("Y-m-d H:i:s");

        $args['created_at'] = $timestamp_submitted;

        $args['updated_at'] = $timestamp_submitted;

        return DB::table('monitoring')->updateOrInsert(
            [
                'worker_id'   => $worker_id,
                'application' => $args['application'],
                'type'        => $args['type']
            ],
            $args
        );
    }

    /**
     *
     * Retrieve the monitoring details of a specific workstation
     *
     * @param String $worker_id
     * @return
     * @throws conditon
     **/
    public function getAll($worker_id ){
        return DB::table('monitoring') -> where([
            'worker_id' => $worker_id
        ]) -> get();
    }

    /**
     *
     * Clears the monitoring statistics
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function clear($worker_id, $type = 'AUTO', $application = false){
        $conditions = [
            'worker_id' => $worker_id,
            'type'      => $type
        ];

        if( $application )
            $conditions['application'] = $application;

        return DB::table('monitoring')
            -> where( $conditions )
            -> delete();
    }

    // public function getMonitoringData($worker_id, $application, $timestamp) {
    public function getMonitoringData($id) {

        $query = null;
        // old query
        // if ($timestamp && $application) {
        //     $query = DB::table('monitoring')
        //         -> where('worker_id', $worker_id)
        //         -> where('created_at', $timestamp)
        //         -> where('application', $application)
        //         -> get();
        // }
        // return $query;
        if($id) {
            $query = DB::table('monitoring')
            -> where('id', $id)
            -> get();
        }
        return $query;
    }
}
