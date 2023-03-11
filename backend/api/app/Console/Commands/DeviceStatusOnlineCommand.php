<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Container\Container;
use App\Modules\Applications\Services\DeviceStatusService;
use App\Modules\WorkstationModule\Events\DeviceStatusBroadcast;
use Illuminate\Support\Facades\Log;

class DeviceStatusOnlineCommand extends Command
{
    /** @var DeviceStatusService $service  */
    protected $service;
    /**
     * The name and signature of the console command.
    *
    * @var string
    */
    protected $signature = 'agent:status-check';

    /**
     * The console command description.
    *
    * @var string
    */
    protected $description = 'Check for Online Agents';

    /**
     * Create a new command instance.
    *
    * @return void
    */
    public function __construct(Container $container){
        $this -> service = $container -> get('DeviceStatusService');
        parent::__construct();
    }

    /**
     * Execute the console command.
    *
    * @return mixed
    */
    public function handle(){
        $devices = $this -> service -> retrieveRedisData();

        $dateTime = time();
        
        $i=0;
        if(!empty($devices))
        {
            foreach($devices as $device)
            {
                $timeStarted = $device['dateTime'];
                $count = $device['count'];
                $employee_id = $device['worker_id'];

                if($count >= 2)
                {
                    echo 'This device should be removed '.PHP_EOL;
                    $this -> service -> removeRedisData('DeviceStatus.'.$device['id']);
                    $this -> service -> updateOnlineStatus($device['id']);
                    //event( new DeviceStatusBroadcast($device['id']));
                   Log::info('Device Removal '.$device['id']); 
                }else{
                    $redisDetails = [
                        'id'        => $device['id'],
                        'worker_id' => $employee_id,
                        'dateTime'  => $timeStarted,
                        'count'     => $count + 1
                    ];

                    $this -> service -> removeRedisData('DeviceStatus.'.$device['id']);
                    $this -> service -> storeRedis($device['id'], $redisDetails);
                    // Log::info('Broadcasting '.$device['id'].' for '.($count+1).' times');
                    event( new DeviceStatusBroadcast($device['id']));
                }
              
            }
        }
    }

    function statusCheck($device_id, $device)
    {
        $updated_at = new \DateTime($device->updated_at);
        $updated_at->add(new \DateInterval('PT20H'));
        $now = date('Y-m-d H:i:s');

        echo 'db - '. $device->updated_at.PHP_EOL;
        echo '+20H - '.$updated_at->format('Y-m-d H:i:s').PHP_EOL;
        echo 'now - '.$now.PHP_EOL;

        if(strtotime($now) > strtotime($updated_at->format('Y-m-d H:i:s')))
        {
            $this -> service -> removeRedisData($device_id);
            $this -> service -> updateOnlineStatus($device->session_id);
            echo PHP_EOL;
            echo 'Remove this device - '.$device_id.PHP_EOL;
            Log::info('Remove this device - '.$device_id);
            return false;
        }
        if($device->session_id=="")
        {
            $this -> service -> updateOnlineStatus($device->session_id);
        }
        return true;
    }
}