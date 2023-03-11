<?php

namespace App\Modules\WorkstationModule\Events;
use App\Modules\WorkstationModule\Events\AgentWorkstationTriggerBroadcast;

class AgentHostfileBroadcast extends AgentWorkstationTriggerBroadcast {

    /** @var String application */
    public $url;
    public $password;


    /**
     *
     * Constructor dependencies
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct($session_id, $url, $password ){
        parent::__construct( $session_id );
        $this -> url = $url ;
        $this -> password = $password;
    }

}
