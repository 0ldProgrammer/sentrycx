<?php

namespace App\Modules\WorkstationModule\Events;
use App\Modules\WorkstationModule\Events\AgentWorkstationTriggerBroadcast;

class AgentAppUpdateBroadcast extends AgentWorkstationTriggerBroadcast {

    /**
     *
     * Constructor dependencies
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct($session_id  ){
        parent::__construct( $session_id );
    }

}
