<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\WorkstationModule\Services\AgentConnectionService;
use App\Modules\WorkstationModule\Events\AgentLiteModeBroadcast;
use Illuminate\Container\Container;


class DisableAgentCommand extends Command
{
    /** @var AgentConnectionService $service  */
    protected $service;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agent:disable {worker_id} {disable=true}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tag the agent as disable to making the application execute with less diagnostics';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Container $container){
        $this -> service = $container -> get('AgentConnectionService');
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(){
        $worker_id = $this -> argument('worker_id');
        $disable = $this ->argument('disable');

        echo "Updating agent $worker_id ";

        $this -> service -> disableAgent( $worker_id );

        $session_id = $this -> service -> getAgentSession( $worker_id );

        event( new AgentLiteModeBroadcast($session_id, $disable));

        echo ".. DONE" . PHP_EOL;
    }
}
