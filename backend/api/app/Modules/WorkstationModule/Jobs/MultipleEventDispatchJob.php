<?php 

namespace App\Modules\WorkstationModule\Jobs;

use App\Jobs\Job;
use App\Modules\WorkstationModule\Events\AgentHostfileBroadcast;
use App\Modules\WorkstationModule\Services\AgentConnectionService;
use Illuminate\Container\Container;

class MultipleEventDispatchJob extends Job
{
    /** @Array $sessionList */
    protected $sessionList = [];
    
    // /** @var AgentConnectionService $service description */
    // protected $var = null;
    
    /**
     * Constructor method
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct(Array $args){
        $this -> sessionList = $args['sessions'];
    }
}
