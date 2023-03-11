<?php

namespace App\Modules\Maintenance\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use  \Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class AgentLoginDeploymentApplicationBroadcast extends Event implements ShouldBroadcastNow {
    use SerializesModels;

    public $session_id;
    public $data;


    /**
     * Constructor Method
     *
     * @param Array $data
     * @return type
     * @throws conditon
     **/


    public function __construct( $session_id, $data ) {
        $this->session_id = $session_id;
        $this->data = $data;
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