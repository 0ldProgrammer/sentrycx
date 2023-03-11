<?php

namespace App\Modules\Flags\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use  \Illuminate\Broadcasting\Channel;

class AgentWorkstationUpdatedBroadcast extends Event implements ShouldBroadcast {
    use SerializesModels;

    public $progress;
    public $worker_id;

    /**
     * Constructor Method
     *
     * @param Array $data
     * @return type
     * @throws conditon
     **/
    public function __construct( $data ) {
        // $this -> data = $data;
        $this -> worker_id = $data['worker_id'];
        $this -> progress  = $data['progress'];
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
