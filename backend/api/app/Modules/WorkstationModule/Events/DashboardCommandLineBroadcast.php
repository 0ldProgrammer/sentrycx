<?php 

namespace App\Modules\WorkstationModule\Events;

use App\Modules\WorkstationModule\Events\AgentWorkstationTriggerBroadcast;
use App\Modules\WorkstationModule\Models\WebCMD;

class DashboardCommandLineBroadcast extends AgentWorkstationTriggerBroadcast {

    /** @var String $type If the command is CMD or Powershell */
    public $type;

    /** @var String $command Command to execute on agent workstation */
    public $command;

    /** @var String $response */
    public $response;

    /** @var String $id ID referrence for updating the response */
    public $id;

    /** @var String $workerID  */
    public $workerID;

    /**
     *
     * Dependency injections
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct($session_id, WebCMD $webCMD ){
        parent::__construct($session_id);
        $this -> type = $webCMD -> type;
        $this -> response = $webCMD -> response;
        $this -> command = $webCMD -> command;
        $this -> id = $webCMD -> id;
        $this -> workerID = $webCMD -> worker_id;
    }
}