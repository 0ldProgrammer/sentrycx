<?php

namespace App\Modules\WorkstationModule\Services;

use Illuminate\Support\Facades\DB;

use App\Modules\WorkstationModule\Models\EventApprovals;

/**
 * Application Service
 */
class EventApprovalService
{

    public function __construct() {}

    public function insert($requested_by, $event, $agent_name, $params, $worker_id)
    {
        // Validate the request...

        $eventApproval = new EventApprovals;

        $eventApproval->requested_by = $requested_by;
        $eventApproval->event = $event;
        $eventApproval->status = false;
        $eventApproval->agent_name = $agent_name;
        $eventApproval->approved_by = '';
        $eventApproval->params = $params;
        $eventApproval->worker_id = $worker_id;

        if($eventApproval->save()):
            return true;
        endif;
    }

    public function updatePost($conditions, $status, $approved_by)
    {
        $eventApproval = new EventApprovals;

        $eventApproval->exists = true;
        $eventApproval->id = $conditions;
        $eventApproval->status = $status;
        $eventApproval->approved_by = $approved_by;

        if($eventApproval->save()):
            return true;
        endif;
    }


    public function query($conditions = "", $page = 1, $per_page = 20 ){
        $query = DB::table('event_approvals') -> orderBy('created_at', 'DESC');
        if($conditions!="")  
            $query->where('event', 'like', "%$conditions%");  

        return $query -> paginate($per_page, ['*'], 'page', $page );
    }

    public function getEventApprovalByApprovalID($id){
        $query = DB::table('event_approvals');
        $query -> where('id', $id);
        return $query -> first();
    }
}