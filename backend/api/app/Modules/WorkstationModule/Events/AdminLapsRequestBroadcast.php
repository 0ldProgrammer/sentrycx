<?php

namespace App\Modules\WorkstationModule\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use  \Illuminate\Broadcasting\Channel;

class AdminLapsRequestBroadcast extends Event implements ShouldBroadcast {
    use SerializesModels;

    public $callback_url;
    public $station_name;
    public $params;

    /**
     * Constructor Method
     *
     * @param Array $data
     * @return type
     * @throws conditon
     **/
    public function __construct( $data ) {
        $this -> callback_url = $data['callback_url'];
        $this -> station_name = $data['station_name'];
        $this -> params = $data['params'];

    }

    /**
     * Get the channels the event should be broadcast on
     *
     * @param Type $var Description
     * @return  Illuminate\Broadcasting\Channel
     * @throws conditon
     **/
    public function broadcastOn(){
        // return new Channel( getenv('SOCKET_CHANNEL_ADMIN_LAPS') );
        return new Channel( 'admin-laps' );
    }
}
