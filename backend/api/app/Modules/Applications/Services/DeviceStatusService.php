<?php

namespace App\Modules\Applications\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class DeviceStatusService {


    function updateOnlineStatus($session_id)
    {
        if(DB::table('agent_connections')->where('session_id',$session_id)->update(array('is_active'=>false)))
        {
            return true;
        }else{
            return false;
        }
    }

    function getOnlineAgents()
    {
        $query = DB::table('agent_connections')
                    -> select('session_id','worker_id', 'agent_name','updated_at')
                    -> orderBy('updated_at','DESC')
                    -> where('is_active', true);

        return $query->get();
    }

    function setRedis($socketId, $dateTime)
    {
        if(Redis::rPush('DeviceStatus.'.$socketId, $dateTime))
        {
            echo 'DeviceStatus.'.$socketId.PHP_EOL;
            return true;
        }else{
            return false;
        }
    }

    function getRedis($socketId=null)
    {
        // $unixTime = Redis::lRange('DeviceStatus.'.$socketId, 0, -1);
        $unixTime = Redis::keys('DeviceStatus.*');
        if($socketId!=null)
        {
            $unixTime = Redis::lRange($socketId, 0, -1);
        }
        
        return $unixTime;

    }

    function storeRedis($socketId, $details)
    {
        Redis::hmset('DeviceStatus.'.$socketId, $details);
    }

    function retrieveRedisData()
    {
        $keys = Redis::keys('DeviceStatus.*');
        $clients = [];
        if(!empty($keys))
        {
            foreach ($keys as $key) {
                $stored = Redis::hgetall($key);
                $clients[] = $stored;
            }
        }
        return $clients;
    }

    function getRedisData($socketId, $details = 'id')
    {
        $key = 'DeviceStatus.'.$socketId; 
        $data = Redis::hget($key, $details);
        return $data;
    }

    function removeRedisData($socketId)
    {
        return Redis::del($socketId);

    }

}