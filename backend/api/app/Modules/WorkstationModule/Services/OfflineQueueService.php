<?php 

namespace App\Modules\WorkstationModule\Services;

use Illuminate\Support\Facades\DB;

class OfflineQueueService {

    /**
     *
     * Adds to offline queue
     *
     * @param Array $args Description
     * @return type
     * @throws conditon
     **/
    public function queueEvents($args ){
        return DB::table('offline_queues')->insert( $args );
    }

    /**
     *
     * Retrive the offline queued events
     *
     * @return type
     * @throws conditon
     **/
    public function getQueue(){
        return DB::table('offline_queues as oq')
            -> leftJoin('agent_connections as ac', 'ac.worker_id', '=', 'oq.worker_id')
            -> addSelect(['ac.worker_id', 'session_id','oq.id as queue_id', 'event_name', 'parameters'])
            -> where('ac.is_active', TRUE)
            -> get();
    }

    /**
     *
     * Clear the queues after dispatching 
     *
     * @param Array $queue_ids 
     * @return type
     * @throws conditon
     **/
    public function clearQueue($queue_ids = []){
        return DB::table('offline_queues')
            -> whereIn('id', $queue_ids)
            -> delete();
    }

}