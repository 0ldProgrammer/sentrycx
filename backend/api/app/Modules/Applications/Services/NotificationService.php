<?php

namespace App\Modules\Applications\Services;
use Illuminate\Support\Facades\DB;
/**
 * Application Service
 */
class NotificationService
{
    /**
     * Setup dependency injections here
     *
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct()
    {

    }


    /**
     * Retrieve the list of applications
     *
     * @return type
     * @throws conditon
     **/
    public function getNotifications($worker_id)
    {
        return $results = DB::table('notifications')->where('worker_id', $worker_id)->where('is_active', true)->orderBy('id')->get();
    }

    /**
     * Submit Workstation info
     *
     * @return type
     * @throws conditon
     **/
    public function addNotification( $data ) {
        $timestamp_submitted = date("Y-m-d H:i:s");
        DB::table('notifications')->insert(
            [
                'worker_id'     => $data['worker_id'],
                'title'         => $data['title'],
                'message'       => $data['message'],
                'session_id'    => $data['session_id'],
                'url'           => $data['url'],
                'created_at'    => $timestamp_submitted
            ]
        );
        return $id = DB::getPdo()->lastInsertId();
    }
        /**
     * Submit Workstation info
     *
     * @return type
     * @throws conditon
     **/
    public function updateNotification( $data ) {
        $timestamp_submitted = date("Y-m-d H:i:s");
        if($data['worker_id'] == 0)
        {
            DB::table('notifications')
            ->where('id', $data['notification_id'])
            ->update(array('updated_at' => $timestamp_submitted,'is_active' => false ));
        }
        else
        {
            DB::table('notifications')
            ->where('worker_id', $data['worker_id'])
            ->update(array('updated_at' => $timestamp_submitted, 'is_active' => false ));
        }
    }
}
