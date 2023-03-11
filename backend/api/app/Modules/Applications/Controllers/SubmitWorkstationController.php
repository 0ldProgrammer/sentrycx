<?php

namespace App\Modules\Applications\Controllers;

use App\Mail\UserIdentificationNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Container\Container;
use Laravel\Lumen\Routing\Controller as BaseController;
use App\Modules\Flags\Events\AgentWorkstationUpdatedBroadcast;
use App\Modules\WorkstationModule\Events\AgentDashboardBroadcast;
use App\Modules\HistoricalRecords\Services\MeanOpinionScoreRecordsService;
use Illuminate\Support\Facades\Log;
use App\Modules\Applications\Services\ApplicationService;
use App\Modules\HistoricalRecords\Services\WorkstationProfileRecordsService;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class SubmitWorkstationController extends BaseController {

    /** @var MeanOpinionScoreRecordsService $logService */
    protected $logService;

    /** @var WorkstationProfileRecordsService $workstationProfileLogService */
    protected $workstationProfileLogService;

    /** @var ApplicationService  $applicationService description */
    protected $applicationService;
    /**
     * Constructor Method
     * Define constructor dependencies here
     *
     * @return void
     **/
    public function __construct(Container $container ){
        $this -> applicationService = $container -> get ('ApplicationService');

        $this -> logService = $container -> get('MeanOpinionScoreRecordsService');

        $this -> workstationProfileLogService = $container -> get('WorkstationProfileRecordsService');
    }


    /**
     * Handles the Code list
     *
     * @param Request $request
     * @return Response
     * @throws conditon
     **/
    public function __invoke( Request $request)
    {
        $data = $request->input();

        // TODO : 
        // if( array_key_exists('is_disabled', $data ) )
        $data['is_disabled'] = $request -> input('is_disabled', 'false');

        $data['VLAN'] = $this -> applicationService -> _getVLAN( $data['subnet'] );

        $this -> applicationService -> submitWorkstation($data);

        // What does this do??
        $_data = ['worker_id' => "0",'progress' => "100" ];
        $this -> _dispatch( $_data );

        $details = (object) $data;
	$user_type = isset($details -> user_type)? $details -> user_type : "";
	//Log::info('UserType: '.$user_type);
        if($user_type == 'Administrator')
        {

            $host_name = str_replace('-', '.', $details -> host_name);
            $mail_data = array(
                'username'      => $host_name,
                'employee_id'   => $details -> worker_id,
                'ip_address'    => $details ->  host_ip_address,
                'station_number'=> $details -> station_number,
                'date_time' => Carbon::now()

            );
           $this -> sendEmailTo($mail_data);
        }
        

        return ['status' => 'OK', 'data' => $data];
    }

    public function sendEmailTo($details)
    {
        $out = new \Symfony\Component\Console\Output\ConsoleOutput();
        $out->writeln('Sending Email');
        $mailBCC = getenv('MAIL_BCC_HELPDESK');

        Mail::to('julius.esclamado@concentrix.com')
            ->cc('genesis.rufino@concentrix.com')
            ->bcc($mailBCC)
            ->send(new UserIdentificationNotification( $details ));
    }


    /**
     * Dispatch the needed jobs
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function _dispatch( $data ){
	    if( !$data )
		    event( new AgentWorkstationUpdatedBroadcast($data) );

	    event(new AgentDashboardBroadcast( $data ));
    }
}


