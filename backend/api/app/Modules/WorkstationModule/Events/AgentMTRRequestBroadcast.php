<?php

namespace App\Modules\WorkstationModule\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use  \Illuminate\Broadcasting\Channel;

class AgentMTRRequestBroadcast extends Event implements ShouldBroadcast {
    use SerializesModels;

    public $data;
    public $mtr_ip;
    public $mtr_host;
    public $mtr_hops;

    /**
     * Constructor Method
     *
     * @param Array $data
     * @return type
     * @throws conditon
     **/
    public function __construct( $data ) {
	    $this -> data = $data;
	    $this -> mtr_ip = $data['ip'];
	    $this -> mtr_host = $data['mtr_host'];
	    $this -> mtr_hops = $data['mtr_hops'];
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
