<?php

namespace App\Modules\WorkstationModule\Controllers;

use Illuminate\Container\Container;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\WorkstationModule\Services\CommandLineService;

class UpdateVersionLogsController extends Controller {

  
    /** @var CommandLineService $service */
    protected $service;

    /*
     *
     * Constructor dependency method
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct(Container $container ){
        $this -> service = $container -> get('CommandLineService');
    }
    /**
     *
     * Handles the retrieval of agent connection details by id
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/

    public function __invoke(Request $request){
        $page = $request -> query('page', 1);

        $details = $this -> service -> getUpdateLogs($page);
        echo '<table style="width:100%;"> <tr><td>#</td><td>Worker ID</td><td>Name</td><td>Account</td><td style="text-align:center;">Attempts</td><td>Session ID</td></tr>';
        $i = 0;
        foreach($details as $d)
        {
            $i++;
            echo '<tr><td>'.$i.'</td><td>'.$d->worker_id.'</td><td>'.strtoupper($d->lastname.', '.$d->firstname).'</td><td>'.$d->account.'</td><td style="text-align:center;">'.$d->no_attempts.'</td><td>'.$d->session_id.'</td></tr>';
        }
        echo '</table>'; 
    }

}
