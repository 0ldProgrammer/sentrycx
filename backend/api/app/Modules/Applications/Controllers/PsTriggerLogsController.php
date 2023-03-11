<?php

namespace App\Modules\Applications\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Container\Container;
use Laravel\Lumen\Routing\Controller as BaseController;

class PsTriggerLogsController extends BaseController {

    /** @var App\Modules\Applications\Services\PsTriggerLogsService  $PsTriggerLogsService description */
    protected $PsTriggerLogsService;
    /**
     * Constructor Method
     * Define constructor dependencies here
     *
     * @return void
     **/
    public function __construct(Container $container ){
        $this -> PsTriggerLogsService = $container -> get ('PsTriggerLogsService');
    }


    /**
     * Handles the Code list
     *
     * @param Request $request
     * @return Response
     * @throws conditon
     **/
    public function __invoke( Request $request)
    {

        return $this -> PsTriggerLogsService ->  logTriggeredEvent($request);
    }

    public function triggerLogsList(Request $request)
    {
        $page = $request -> query('page', 1);
        $workstation = $request -> query('workstation', '');

        $details = $this -> PsTriggerLogsService -> triggerLogsList($page, $workstation);
        echo '<table style="width:100%;"> <tr><td>#</td><td>Worker ID</td><td>Name</td><td>username</td><td>Workstation ID</td><td>Triggered Event</td><td>Date Triggered</td></tr>';
        $i = 0;
        foreach($details as $d)
        {
            $i++;
            echo '<tr><td>'.$i.'</td><td>'.$d->worker_id.'</td><td>'.strtoupper($d->lastname.', '.$d->firstname).'</td><td>'.$d->username.'</td><td>'.$d->workstation_name.'</td><td>'.$d->triggered_event.'</td><td>'.$d->date_triggered.'</td></tr>';
        }
        echo '</table>'; 
    }

    
}