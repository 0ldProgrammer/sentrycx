<?php 

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Container\Container;
use App\Modules\WorkstationModule\Services\AgentConnectionService;

class AgentConnectionProfileSyncCommand extends Command {
    /** @var AgentConnectionService $service  */
    protected $service;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agent:update-profile';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates the agent_connection profile by doing a sync from cnx_employees';

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
        echo "Updating outdated agent profile" . PHP_EOL;
        $this -> service -> updateAgentProfile();
        echo "Done update." . PHP_EOL;
    }
}