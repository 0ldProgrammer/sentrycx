<?php

namespace App\Modules\HistoricalRecords\Controllers;

use App\Modules\HistoricalRecords\Services\SecureCXRecordsService;
use App\Modules\Applications\Services\ApplicationService;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Laravel\Lumen\Routing\Controller as BaseController;

use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SecureCXSaveController extends BaseController {

    /** @var SecureCXRecordsService $var description */
    protected $service;

    protected $applicationService;

    /**
     *
     * Constructor dependencies
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct(Container $container){
        $this -> service = $container -> get('SecureCXRecordsService');
        $this -> applicationService = $container -> get('ApplicationService');
    }

    /**
     *
     * Handles fetching of SecureCX
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __invoke(Request $request){
	    $current_date = date('Y-m-d H:i:s');
        $new_securecx_logs = array();
        $new_smart_monitoring_logs = array();
        $worker_id = $request -> route('worker_id');
        $data = $request->input();

        if ( isset($data['speedtest_logs'])) {
            $speedtest_logs = $data['speedtest_logs'];
            $speedtest_logs['worker_id'] = $worker_id;

            $now = Carbon::now();
            $mLog = "speedTestLogs : workerId : {$worker_id} : date: {$now}";
            //Log::info($mLog);

            $this -> applicationService -> logSpeedtest($speedtest_logs);
        }

        if ( isset($data['mos_logs'])) {
            $mos_logs = $data['mos_logs'];
            $mos_logs['worker_id'] = $worker_id;

            $now = Carbon::now();
            $mLog = "mosLogs : workerId : {$worker_id} : date: {$now}";
            //Log::info($mLog);

            $this -> applicationService -> logMeanOpinionScore( $mos_logs );
        }

        if ( isset($data['securecx_logs'])) {
            $securecx_logs = $data['securecx_logs'];

            foreach ($securecx_logs as $value){
                $value['worker_id'] = $worker_id;
                $value['created_at'] = $current_date;
                $value['updated_at'] = $current_date;
                if(isset($value['workstation_id'])) unset($value['workstation_id']); // payload changed, added workstation_id in securecx_logs for api v2. mysql table logs_securecx has no workstation_id column. unset needed
                array_push($new_securecx_logs, $value);
            }

            $now = Carbon::now();
            $mLog = "securecxLogs : workerId : {$worker_id} : date: {$now}";
            //Log::info($mLog);

            $this -> service -> log( $new_securecx_logs );
        }

        if (isset($data['smart_monitoring_logs'])) {
            $smart_monitoring_logs = $data['smart_monitoring_logs'];

            foreach ($smart_monitoring_logs as $value){
                $value['worker_id'] = $worker_id;
                array_push($new_smart_monitoring_logs, $value);
            }

            $now = Carbon::now();
            $mLog = "smartMonitoringLogs : workerId : {$worker_id} : date: {$now}";
            //Log::info($mLog);
            
            $this -> applicationService -> logSmartMonitoring($new_smart_monitoring_logs);
        }

        return ['status' => 'OK', 'data' => $this -> service -> getModel() ];
    }
}
