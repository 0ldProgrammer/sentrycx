<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Container\Container;
use App\Modules\WorkstationModule\Services\PotentialTriggerService;
use App\Modules\Workday\Services\EmployeeService;
use App\Modules\WorkstationModule\Events\AgentScheduledWipeoutBroadcast;
use App\Modules\WorkstationModule\Events\AdminLapsRequestBroadcast;
use App\Modules\WorkstationModule\Events\AgentRemoveScheduledWipeoutBroadcast;
use App\Modules\WorkstationModule\Services\AgentConnectionService;
use App\Modules\WorkstationModule\Services\OfflineQueueService;
use Illuminate\Support\Carbon;

class AutomatedWipeoutCommand extends Command
{
    /** @var PotentialTriggerService $potentialTriggerService */
    protected $potentialTriggerService;

    /** @var EmployeeService $employeeService */
    protected $employeeService;

    /** @var AgentConnectionService $agentConnectionService */
    protected $agentConnectionService;

    /** @var OfflineQueueService $offlineQueueService */
    protected $offlineQueueService;


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agent:scheduled-wipeout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends a schedule wipeout trigger to the agent';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Container $container)
    {
        $this -> potentialTriggerService = $container -> get('PotentialTriggerService');
        $this -> employeeService = $container -> get('EmployeeService');
        $this -> agentConnectionService = $container -> get('AgentConnectionService');
        $this -> offlineQueueService = $container -> get('OfflineQueueService');
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(){
        $resigned_employees = $this -> employeeService -> getResignedEmployee();

        $updated_resignations = $this -> employeeService -> getUpdatedResignation();

        echo "RESIGNED EMPLOYEE : " . $resigned_employees -> count() . PHP_EOL ;

        echo "UPDATED EMPLOYEE : " . $updated_resignations -> count() . PHP_EOL;

        // $this -> saveAsPotentialTriggers( $resigned_employees );

        $this -> saveAsPotentialTriggers( $updated_resignations, true );
    }

    /**
     *
     * Logs the event to potential triggers
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function saveAsPotentialTriggers($employees, $update = false ){
        $event_name = 'PC Wipeout';
        $conditions = null;
        foreach( $employees as $employee ){
            $execution_date = new Carbon( $employee -> resignation_date );# 
            $execution_date-> add('2', 'days');
            
            $data = [
                'event' => $event_name,
                'triggered_by' => 'SYSTEM',
                'datetime_triggered' => $employee -> resignation_date,
                'agent_name' => $employee -> firstname. " ". $employee -> lastname ,
                'worker_id'  => $employee -> employee_number,
                'email'      => $employee -> email,
                'position'   => $employee -> job_profile,
                'site'       => $employee -> location_name,
                'manager'    => $employee -> supervisor_full_name,
                'execution_type' => 0,
                'remarks'    => 'From schedule',
                'execution_date' => $execution_date
            ];

            if( $update )
                $conditions = [
                    'worker_id' => $employee -> employee_number,
                    'event' => $event_name
                ];

            

            if( !$employee -> resignation_date ){
                $this -> cancelWipeout( $employee -> employee_number );
                $this -> potentialTriggerService -> remove([
                    'event' => $event_name,
                    'worker_id' => $employee -> employee_number
                ]);
                return;
            }
            $this -> potentialTriggerService -> save( $data, $conditions );
            $this -> dispathBroadcast( $employee -> employee_number, $execution_date );
        }
    }

    /**
     *
     * Cancels the scheduled wipeout
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function cancelWipeout( $worker_id ) {

        $connection = $this -> agentConnectionService ->  getConnectionByEmployee( $worker_id );

        if( $connection -> is_active ){
            echo "DISPATCHING CANCEL $worker_id : ONLINE ";
            event( new AgentRemoveScheduledWipeoutBroadcast($connection -> session_id) );
            return;
        }

        $events = [];
        $events[] = [
            'worker_id' => $connection -> worker_id,
            'event_name' => "App\Modules\WorkstationModule\Events\AgentRemoveScheduledWipeoutBroadcast",
            'parameters' => "{}"
        ];
        echo "DISPATCHING $worker_id : OFFLINE ";

        $this -> offlineQueueService -> queueEvents($events);

    }


    /**
     *
     * Sends a broadcast for scheduled wipeout
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function dispathBroadcast( $worker_id, $execution_date ) {
        $connection = $this -> agentConnectionService ->  getConnectionByEmployee( $worker_id );
        $params = [ 'execution_date' => $execution_date -> format('Y-m-d H:i:s') ];
        $args = [
            'callback_url' => getenv('APP_URL') . "workstation/{$worker_id}/wipeout-scheduled",
            'station_name' => $connection -> station_name,
            'params' => $params
        ];

        echo '<pre>';
        print_r($args);

        event( new AdminLapsRequestBroadcast( $args ) );
        echo "DONE DISPATCHING TO ADMIN LAPS APP";
    }

    /**
     *
     * Retrieve the password for local admin
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function getPassword(){
        return 'PASSWORD';
    }
}
