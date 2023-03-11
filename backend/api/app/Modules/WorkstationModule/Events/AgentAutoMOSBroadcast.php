<?php

namespace App\Modules\WorkstationModule\Events;
use App\Modules\WorkstationModule\Events\AgentWorkstationTriggerBroadcast;

class AgentAutoMOSBroadcast extends AgentWorkstationTriggerBroadcast {

    /** @var String status */
    public $status;


    /**
     *
     * Constructor dependencies
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct($session_id, $status = 'STOP'){
        parent::__construct( $session_id );
        $this -> status  = $status;
    }

}
