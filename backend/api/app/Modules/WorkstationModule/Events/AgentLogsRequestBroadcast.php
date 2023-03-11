<?php

namespace App\Modules\WorkstationModule\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use  \Illuminate\Broadcasting\Channel;

class AgentLogsRequestBroadcast extends Event implements ShouldBroadcast {
    use SerializesModels;

    public $date_start;

    public $date_end;

    public $type;

    public $filename;

    public $session_id;

    public $keyword;

    /**
     * Constructor Method
     *
     * @param Array $data
     * @return type
     * @throws conditon
     **/
    public function __construct( $data ) {
        $this -> date_start = $data['date_start'];
        $this -> date_end   = $data['date_end'];
        $this -> type       = $data['type'];
        $this -> filename   = $data['filename'];
        $this -> session_id = $data['session_id'];
        $this -> keyword    = $data['keyword'];
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
