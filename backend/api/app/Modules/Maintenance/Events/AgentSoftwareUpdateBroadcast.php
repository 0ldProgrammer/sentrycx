<?php

namespace App\Modules\Maintenance\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use  \Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class AgentSoftwareUpdateBroadcast extends Event implements ShouldBroadcastNow {
    use SerializesModels;

    public $session_id;
    public $update_id;
    public $patch_name;


    /**
     * Constructor Method
     *
     * @param Array $data
     * @return type
     * @throws conditon
     **/


    public function __construct( $session_id, $update_id, $patch_name ) {
        $this->session_id = $session_id;
        $this->update_id = $update_id;
        $this->patch_name = $patch_name;
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