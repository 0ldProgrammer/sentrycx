<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\WorkstationModule\Services\AgentConnectionService;
use Illuminate\Container\Container;

class AgentConnectionLoggerCommand extends Command
{
    /** @var AgentConnectionService $service  */
    protected $service;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agent:logger';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Statistics of connected agent , create a copy of agent info and log to database';

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
        $stats = $this -> service -> getConnectionStatsOverall(null);

        foreach( $stats as $stat ){
            $stat -> type = "HALF_HOURLY";
            $this -> service -> generateStats($stat);
        }

        $this -> service -> logOnlineAgents();

        echo "Done generating stats . See online_stats database table" . PHP_EOL;
    }
}
