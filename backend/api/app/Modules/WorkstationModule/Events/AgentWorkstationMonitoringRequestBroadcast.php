<?php

namespace App\Modules\WorkstationModule\Events;
use App\Modules\WorkstationModule\Events\AgentWorkstationTriggerBroadcast;

class AgentWorkstationMonitoringRequestBroadcast extends AgentWorkstationTriggerBroadcast {

    /** @var String application */
    public $application;


    /**
     *
     * Constructor dependencies
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct($session_id, $application ){
        parent::__construct( $session_id );
        $this -> application = $application ;
    }

}
