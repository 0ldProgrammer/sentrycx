<?php

namespace App\Modules\WorkstationModule\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use  \Illuminate\Broadcasting\Channel;

class AgentWorkstationRequestBroadcast extends Event implements ShouldBroadcast {
    use SerializesModels;

    public $data;
    public $redflag_id;

    /**
     * Constructor Method
     *
     * @param Array $data
     * @return typea
     * @throws conditon
     **/
    public function __construct( $data ) {
	    $this -> data = $data;
	    $this -> redflag_id = $data['redflag_id'];
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
