<?php 

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Container\Container;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use App\Modules\WorkstationModule\Services\OfflineQueueService;

class OfflineQueue extends Command {

    /** @var OfflineQueueService $service */
    protected $service;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'offline-queue:work';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Task Scheduler to broadcast specific events to agents that were offline prior to original broadcast.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Container $container ){
        $this -> service = $container -> get('OfflineQueueService');

        parent::__construct();
    }

        /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(){
        $connections = $this -> service -> getQueue();

        $queue_ids = [];

        foreach( $connections as $connection ){
            echo "Dispatching {$connection->event_name} to {$connection->worker_id} ";
            $args = json_decode( $connection -> parameters , true);
            $args['session_id'] = $connection -> session_id;

            event( App::make($connection -> event_name, $args ) );
            $queue_ids[] = $connection -> queue_id;
        }

        $this -> service -> clearQueue( $queue_ids );

        echo "Done dispatching " . count( $connections ) . " events ";
    }
}