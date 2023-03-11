<?php

namespace App\Modules\Applications\Controllers;

use App\Mail\HelpdeskTicket;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Container\Container;
use App\Modules\Flags\Jobs\ThresholdCheckJob;
use Laravel\Lumen\Routing\Controller as BaseController;
use App\Modules\Flags\Events\DashboardUpdatedBroadcast;
use App\Modules\HistoricalRecords\Services\MeanOpinionScoreRecordsService;
use Illuminate\Support\Str;

class SubmitCodeController extends BaseController {

    /** @var App\Modules\Applications\Services\ApplicationService  $applicationService description */
    protected $applicationService;

    /** @var MeanOpinionScoreRecordsService $mosService description */
    protected $mosService;
    
    /**
     * Constructor Method
     * Define constructor dependencies here
     *
     * @return void
     **/
    public function __construct(Container $container ){
        $this -> applicationService = $container -> get ('ApplicationService');

        $this -> mosService = $container -> get('MeanOpinionScoreRecordsService');
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
        $data   = $request->input();

        $reference_number['ref_no'] = $this->_generateReferenceNumber();

        array_push($data, $reference_number);
 
        $red_flag_id = $this -> applicationService -> submitCode($data);
        
        if($data['red_flag_id'] == "0")
            return [ 'status' => 'OK', 'red_flag_id' => $red_flag_id ];

        $mailCC  = explode(',', env('MAIL_CC_HELPDESK'));

        $mailBCC = explode(',', env('MAIL_BCC_HELPDESK'));

        if( getenv('APP_ENV') != 'local' )
            Mail::to(env('MAIL_TO_HELPDESK'))
                ->cc($mailCC)
                ->bcc($mailBCC)
                ->send(new HelpdeskTicket( $data ));

        event( new DashboardUpdatedBroadcast );

        $this -> _dispatchThresholdChecker( $data );

        $this -> _logMeanOpinionScore( $data );

        return [ 'status' => 'OK' ];
    }

    /**
     *
     * Logs a MOS entry 
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function _logMeanOpinionScore($data) {
        $params = collect($data) -> only('jitter', 'average_latency', 'mos', 'packet_loss');

        $this -> mosService -> setWorkerID( $data['worker_id'] );

        $this -> mosService -> log( $params );
    }

    /**
     *
     * Dispatch treshold checker
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function _dispatchThresholdChecker( $data ){
        $category_ref = [
            'voice'       => 1,
            'network'     => 2,
            'application' => 3
        ];

        dispatch( new ThresholdCheckJob( [
            'category' => $category_ref[ $data['code_category']],
            'account'  => $data['account'],

        ]));
    }

    private function _generateReferenceNumber(){
        $current_date = date("mdY");
        $random_string = strtoupper(Str::random(5));
        $ref_no = "SNTRY$current_date$random_string";
        
        return $ref_no;
    }
}


