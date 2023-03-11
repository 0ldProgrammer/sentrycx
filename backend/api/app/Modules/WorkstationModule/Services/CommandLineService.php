<?php 

namespace App\Modules\WorkstationModule\Services;

use App\Modules\WorkstationModule\Models\WebCMD;
use App\Modules\WorkstationModule\Models\WorkstationProfile;
use App\Modules\WorkstationModule\Models\VersionUpdateLogs;
use Illuminate\Support\Facades\DB;

use PhpOffice\PhpSpreadsheet\Calculation\Web;

class CommandLineService {

    /** @var WebCMD $webCMD  */
    protected $webCMD;


    /** @var String $worker_id*/
    protected $workerID = null;

    /**
     *
     * Setter for WorkerID
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function setWorkerID($worker_id){
        $this -> workerID = $worker_id;
    }

    /**
     *
     * Constructor Dependencies
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct(WebCMD $webCMD){
        $this -> webCMD = $webCMD;
    }

    /**
     *
     * Log the execution command being sent to the workstation
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function logCommand($command = '', $type = 'CMD'){
        $this -> webCMD -> worker_id = $this -> workerID;

        $this -> webCMD -> type = $type;

        $this -> webCMD -> command = $command;

        $this -> webCMD -> response = '...';

        $this -> webCMD -> save();

        return $this -> webCMD;
    }

    /**
     *
     * Log the Command response 
     *
     * @param Integer $id Description
     * @param Array $data [type, response]
     * @return type
     * @throws conditon
     **/
    public function logResponse($id, $response ){
        $cmd = WebCMD::find($id);
        $cmd -> response = $response;
        $cmd -> save();

        return $cmd;
    }

    /**
     *
     * Retrieve the recent logs 
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function getLogs($type = 'CMD', $limit = 20 ){
        return WebCMD::where('worker_id', $this -> workerID)
            -> where('type', $type)
            -> orderBy('id', 'desc')
            -> limit( $limit )
            -> get();

    }

    public function getDesktopVersion($version)
    {
        return WorkstationProfile::select('workstation_profile.worker_id as wid','account','session_id')
                -> where('desktop_app_version', $version)
                -> join('agent_connections', 'workstation_profile.worker_id','=','agent_connections.worker_id')
                -> get();
    }

    public function logCronUpdate($worker_id, $account, $session_id)
    {
        return VersionUpdateLogs::updateOrCreate(
            ['worker_id' => $worker_id],
            [
                'worker_id' => $worker_id,
                'account'   => $account,
                'session_id'=> $session_id,
                'no_attempts' => DB::raw('no_attempts+1')
            ]
        );
    }

    public function getUpdateLogs($page = 1 , $per_page = 50) {
        $query = VersionUpdateLogs::select('worker_id', 'session_id', 'no_attempts', 'firstname', 'lastname', 'email','account')
                    -> join('cnx_employees','version_update_logs.worker_id','=','cnx_employees.employee_number');

        return $query -> paginate($per_page, ['*'], 'page', $page );

    }

}