<?php

namespace App\Modules\Applications\Services;

use Illuminate\Support\Carbon;
use App\Modules\Applications\Models\TriggerLogs;


class PsTriggerLogsService {


    function logTriggeredEvent($request)
    {
        return TriggerLogs::Create([
            'worker_id'         => $request -> input('worker_id'),
            'username'          => $request -> input('username'),
            'workstation_name'  => $request -> input('workstation_name'),
            'triggered_event'   => $request -> input('triggered_event'),
            'date_triggered'    => Carbon::now()
        ]);
    }


    public function triggerLogsList($page = 1 , $workstation = "", $per_page = 50) {
        
        if($workstation=="")
        {
            $query = TriggerLogs::select('worker_id', 'username',  'firstname', 'lastname', 'workstation_name','triggered_event','date_triggered')
                        -> join('cnx_employees','trigger_logs.worker_id','=','cnx_employees.employee_number');
        }else{
            $query = TriggerLogs::select('worker_id', 'username',  'firstname', 'lastname', 'workstation_name','triggered_event','date_triggered')
            -> join('cnx_employees','trigger_logs.worker_id','=','cnx_employees.employee_number')
            ->where('workstation_name', $workstation);
        }
        return $query -> paginate($per_page, ['*'], 'page', $page );

    }
}