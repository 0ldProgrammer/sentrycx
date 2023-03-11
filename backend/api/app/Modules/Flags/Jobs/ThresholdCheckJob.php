<?php

namespace App\Modules\Flags\Jobs;
use Illuminate\Support\Facades\Log;
use App\Jobs\Job;
use App\Modules\Flags\Events\DesktopNotificationBroadcast;
use App\Modules\Flags\Services\FlagService;
use App\Modules\WorkstationModule\Services\WorkstationService;


class ThresholdCheckJob extends Job
{
    /** @var String $account  */
    private $account ;
    /** @var String $codeID  */
    private $category;
    /** @var String $clientURL  */
    private $clientURL;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( Array $data ){
        $this -> account = $data['account'];
        $this -> category  = $data['category'];
        $this -> clientURL  = getenv('CLIENT_URL');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(FlagService $flagService, WorkstationService $workstationService){
        $flags = $flagService -> getFlagCount(
            $this -> account,
            $this -> category
        );

        $logins = $workstationService -> countAgents( $this -> account );

        $severity = $this -> _getSeverity( $flags, $logins );

        if( !$severity ) 
            return;

        $noc_sessions = $workstationService -> getNOCSessions();
        $this -> _notifyNOC( $noc_sessions, $severity );


    }

    /**
     *
     * Broadcast the notification
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    private function _notifyNOC( Array $noc_ids, $severity = '' ){
        foreach( $noc_ids as $session_id )
            event( new DesktopNotificationBroadcast([
                'title'   => "Threshold Alert",
                'message' => " {$this->account} is having {$severity} flag. Check the Redflag dashboard for details",
                'session_id' => $session_id,
                'url'     => $this -> clientURL . '/dashboard'
            ]));
    }

    /**
     *
     * Get Severity by color
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    private function _getSeverity( $flags = 0, $logins){
        $severity = false;
        $threshold = [
            'YELLOW' => ['min' => 1, 'max' => 4],
            'AMBER'  => ['min' => 5, 'max' => 9],
            'RED'    => ['min' => 10, 'max' => 100000000 ]
        ];

	$percentage = ( $flags/$logins ) * 100 ;
	$percentage = round( $percentage );

	foreach( $threshold as $color => $range ) {
            $check_severity = filter_var(
                $percentage,
                FILTER_VALIDATE_INT,[
                    'options' => [
                        'min_range' => $range['min'],
                        'max_range' => $range['max']
                     ]
                ]
            );

            if( $check_severity ) {
                $severity = $color;
                break;
            }
        }

        return $severity;
    }
}
