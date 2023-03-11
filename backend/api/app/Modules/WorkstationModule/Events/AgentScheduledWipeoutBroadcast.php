<?php

namespace App\Modules\WorkstationModule\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use  \Illuminate\Broadcasting\Channel;

class AgentScheduledWipeoutBroadcast extends Event implements ShouldBroadcast {
    use SerializesModels;

    public $session_id;
    public $password;
    public $execution_date;

    /**
     * Constructor Method
     *
     * @param Array $data
     * @return type
     * @throws conditon
     **/
    public function __construct( $session_id, $data ) {
        $this -> session_id = $session_id;
        $this -> password = $data['password'];
        $this -> execution_date = $data['execution_date'];
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
