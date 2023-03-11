<?php 

namespace App\Modules\HistoricalRecords\Services;

use App\Modules\HistoricalRecords\Models\LogsMOS;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class LogsRecordsService {

    public function getHistoricalLogs($workerID, $time, $date_time) {
        $jitter = array();
        $packet_loss = array();
        $average_latency = array();
        $mos = array();

        if ($time > 0) {
            $date = Carbon::now() -> subHours($time);
        } else {
            $date = Carbon::now() -> subHours(2);
        }

        $get_utc_date_time = $date->format('Y-m-d H:i:s');

        $result = LogsMOS::query()
            -> where('worker_id', $workerID)
            -> where('created_at', '>=', $get_utc_date_time)
            -> orderBy('id', 'DESC')
            -> get();

        if ($time > 0) {
            // 4 or 8 hours
            for ($x = 0; $x <= $time-1; $x++) {
                $i = 0;
                $numItems = count($result);

                $get_local_current_date_time = Carbon::now() -> subHours($x);
                $one_hour_difference = $x + 1;
                $end_range_date = Carbon::now() -> subHours($one_hour_difference);

                if (count($result) > 0) {
                    foreach ($result as $key => $value) {
                        if ($get_local_current_date_time >= $value['created_at'] && $value['created_at'] >= $end_range_date) {
                            array_push($jitter, $value['jitter']);
                            array_push($packet_loss, $value['packet_loss']);
                            array_push($average_latency, $value['average_latency']);
                            array_push($mos, $value['mos']);
                            break;
                        }
                        
                        if(++$i === $numItems) {
                            array_push($jitter, 0);
                            array_push($packet_loss, 0);
                            array_push($average_latency, 0);
                            array_push($mos, 0);
                            break;
                        }
                        
                    }
                } else {
                    array_push($jitter, 0);
                    array_push($packet_loss, 0);
                    array_push($average_latency, 0);
                    array_push($mos, 0);
                }
         
            }
        } else {
            // now
            for ($x = 0; $x <= 1; $x++) {
                $i = 0;
                $numItems = count($result);
                $get_local_current_date_time = Carbon::now() -> subHours($x);
                $one_hour_difference = $x + 1;
                $end_range_date = Carbon::now() -> subHours($one_hour_difference);

                if (count($result) > 0) {
                    foreach ($result as $value) {
                        if ($get_local_current_date_time >= $value['created_at'] && $value['created_at'] >= $end_range_date) {
                            array_push($jitter, $value['jitter']);
                            array_push($packet_loss, $value['packet_loss']);
                            array_push($average_latency, $value['average_latency']);
                            array_push($mos, $value['mos']);
                            break;
                        }
    
                        if(++$i === $numItems) {
                            array_push($jitter, 0);
                            array_push($packet_loss, 0);
                            array_push($average_latency, 0);
                            array_push($mos, 0);
                            break;
                        }
                    }
                } else {
                    array_push($jitter, 0);
                    array_push($packet_loss, 0);
                    array_push($average_latency, 0);
                    array_push($mos, 0);
                }
    
            }
        }

        return array(
            'jitter_results' => $jitter, 
            'packet_loss_results' => $packet_loss,
            'average_latency_results' => $average_latency,
            'mos_results' => $mos 
        );
        
    }

    public function getLabels($time, $date_time) {
        $labels = array();

        if ($time > 0) {
            for ($x=0; $x<=$time-1; $x++) {
                $get_local_current_date_time = Carbon::createFromTimestamp(strtotime($date_time))->subHours($x)->format('H:i');
                array_push($labels, $get_local_current_date_time);
            }
        } else {
            for ($x=0; $x<=1; $x++) {
                $get_local_current_date_time = Carbon::createFromTimestamp(strtotime($date_time))->subHours($x)->format('H:i');
                array_push($labels, $get_local_current_date_time);
            }
        }

        return $labels;
    }
}