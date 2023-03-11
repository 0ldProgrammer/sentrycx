<?php

namespace App\Modules\WorkstationModule\Services;

use Illuminate\Support\Facades\DB;

class MediaDeviceService {


    /**
     *
     * Retrieve the list of sites that needs to be
     * checked for allowing mic/video
     *
     * @param String $account
     * @return type
     * @throws conditon
     **/
    public function getMediaSites($account = ''){
        return DB::table('agent_media_device_sites')
            ->leftJoin('accounts', 'accounts.name', '=', 'agent_media_device_sites.account' )
            ->where('account', $account)
            ->get();
    }

     /**
     *
     * Update the Media Device status of agent
     *
     * @param String $worker_id
     * @param Array $stats
     * @return Boolean
     * @throws conditon
     **/
    public function updateMediaDevice($worker_id, $stats, $field = 'remarks' ){
        $updated_data = array_merge( $stats, ['worker_id' => $worker_id ]);
        $updated_data[ $field ] = json_encode( $updated_data[ $field ] );
        return DB::table('agent_media_device')
            -> updateOrInsert(
                [ 'worker_id' => $worker_id ],
                $updated_data
        );
    }

    /**
     *
     * Retrieve the media device details
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function getMediaDevice($worker_id = 0){
        $devices = DB::table('agent_media_device')->where('worker_id', $worker_id ) -> first();

        if( !$devices )
            return false;

        $devices -> remarks = json_decode( $devices -> remarks );

        return $devices;
    }
}
