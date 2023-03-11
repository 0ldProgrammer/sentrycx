<?php

namespace App\Modules\WorkstationModule\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Broadcasting\Channel;

class AgentWorkstationTriggerBroadcast extends Event implements ShouldBroadcast {
    use SerializesModels;

    public $session_id;

    /**
     * Constructor Method
     *
     * @param Array $data
     * @return type
     * @throws conditon
     **/
    public function __construct( $session_id  ) {
        $this -> session_id = $session_id;
    }

    /**
     * Get the channels the event should be broadcast on
     *
     * @param Type $var Description
     * @return  Illuminate\Broadcasting\Channel
     * @throws conditon
     **/
    public function broadcastOn(){
        return new Channel(getenv('SOCKET_CHANNEL'));
    }

}
