<?php

namespace App\Modules\WorkstationModule\Events;
use App\Modules\WorkstationModule\Events\AgentWorkstationTriggerBroadcast;

class AgentLiteModeBroadcast extends AgentWorkstationTriggerBroadcast {

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
    public function __construct($session_id, $status = true ){
        parent::__construct( $session_id );
        $this -> status  = $status;
    }

}
