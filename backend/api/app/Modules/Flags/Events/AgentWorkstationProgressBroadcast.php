<?php

namespace App\Modules\Flags\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use  \Illuminate\Broadcasting\Channel;

class AgentWorkstationProgressBroadcast extends Event implements ShouldBroadcast {
    use SerializesModels;

    public $data;

    /**
     * Constructor Method
     *
     * @param Array $data
     * @return type
     * @throws conditon
     **/
    public function __construct( $data ) {
        $this -> data = $data;
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
