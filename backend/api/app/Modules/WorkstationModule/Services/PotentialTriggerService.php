<?php

namespace App\Modules\WorkstationModule\Services;

use Illuminate\Support\Facades\DB;

use App\Modules\WorkstationModule\Models\PotentialTrigger;

/**
 * Application Service
 */
class PotentialTriggerService
{

    public function __construct() {}

    /**
     *
     * TODO : Deprecate this and use save instead
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function insert($triggerDetails )
    {
        // Validate the request...

        $potentialTrigger = new PotentialTrigger;

        $potentialTrigger->event = $triggerDetails['event'];
        $potentialTrigger->triggered_by = $triggerDetails['triggered_by'];
        $potentialTrigger->datetime_triggered = $triggerDetails['datetime_triggered'];
        $potentialTrigger->agent_name = $triggerDetails['agent_name'];
        $potentialTrigger->worker_id = $triggerDetails['worker_id'];
        $potentialTrigger->email = $triggerDetails['email'];
        $potentialTrigger->position = $triggerDetails['position'];
        $potentialTrigger->site = $triggerDetails['site'];
        $potentialTrigger->manager = $triggerDetails['manager'];
        $potentialTrigger->execution_date = $triggerDetails['execution_date'];
        $potentialTrigger->execution_type = $triggerDetails['execution_type'];
        $potentialTrigger->remarks = $triggerDetails['remarks'];

        if($potentialTrigger->save())
            return true;

    }

    /**
     *
     * Create or update exiting potential trigger
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function save($triggerDetails, $conditions = null )
    {
        $potentialTrigger = new PotentialTrigger;
        
        if( $conditions ) {
            $query = PotentialTrigger::query();
            foreach( $conditions as $field => $value )
                $query -> where($field, $value );
            
            $potentialTrigger = $query -> first();
        }
        $potentialTrigger->event = $triggerDetails['event'];
        $potentialTrigger->triggered_by = $triggerDetails['triggered_by'];
        $potentialTrigger->datetime_triggered = $triggerDetails['datetime_triggered'];
        $potentialTrigger->agent_name = $triggerDetails['agent_name'];
        $potentialTrigger->worker_id = $triggerDetails['worker_id'];
        $potentialTrigger->email = $triggerDetails['email'];
        $potentialTrigger->position = $triggerDetails['position'];
        $potentialTrigger->site = $triggerDetails['site'];
        $potentialTrigger->manager = $triggerDetails['manager'];
        $potentialTrigger->execution_date = $triggerDetails['execution_date'];
        $potentialTrigger->execution_type = $triggerDetails['execution_type'];
        $potentialTrigger->remarks = $triggerDetails['remarks'];

        return $potentialTrigger->save();
    }

    /**
     *
     * Remove the potential trigger
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function remove($conditions ){
        $query = PotentialTrigger::query();
        foreach( $conditions as $field => $value )
            $query -> where($field, $value );
        
        $query -> delete();
    }

    public function updatePost($conditions, $status, $approved_by)
    {
        $potentialTrigger = new PotentialTrigger;

        $potentialTrigger->exists = true;
        $potentialTrigger->id = $conditions;
        $potentialTrigger->status = $status;
        $potentialTrigger->approved_by = $approved_by;

        if($potentialTrigger->save()):
            return true;
        endif;
    }


    public function query($conditions = "", $page = 1, $per_page = 20 ){

        $value = $conditions['value'];
        $query = DB::table('potential_triggers') -> orderBy('created_at', 'DESC');
        if($conditions['search']=="")
            if($conditions['value']!="")  
                $query->where($conditions['column'], $conditions['value']);  

        if($conditions['search']!="")
            if($conditions['value']!="")  
                $query->where($conditions['column'],'like', "%$value%");  

        return $query -> paginate($per_page, ['*'], 'page', $page );
    }

}