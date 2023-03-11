<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\WorkstationModule\Services\AgentConnectionService;
use Illuminate\Container\Container;

class StatsLoggerCommand extends Command
{
    /** @var AgentConnectionService $service  */
    protected $service;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stats:logger';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Statistics of connected agent and log to database';

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
        $stats = $this -> service -> getConnectionStatsOverall();

        foreach( $stats as $stat )
            $this -> service -> generateStats($stat);

        echo "Done generating stats . See online_stats database table" . PHP_EOL;
    }
}
