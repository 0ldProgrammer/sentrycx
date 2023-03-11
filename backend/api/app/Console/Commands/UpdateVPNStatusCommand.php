<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Container\Container;
use App\Modules\WorkstationModule\Services\WorkstationService;

class UpdateVPNStatusCommand extends Command
{
    /** @var WorkstationService $service  */
    protected $service;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:vpn-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update VPN status to Expired';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Container $container){
        $this -> service = $container -> get('WorkstationService');
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(){
        // update vpn status
        $this -> service -> updateVPNStatusPendingToExpired();
        echo "Done updating vpn status" . PHP_EOL;
    }
}
