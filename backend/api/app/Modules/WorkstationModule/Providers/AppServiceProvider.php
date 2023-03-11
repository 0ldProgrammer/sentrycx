<?php

namespace App\Modules\WorkstationModule\Providers;

use Illuminate\Support\ServiceProvider;
use App\Modules\WorkstationModule\Services\WorkstationService;
use App\Modules\WorkstationModule\Services\EventLogService;
use App\Modules\WorkstationModule\Services\MonitoringService;
use App\Modules\WorkstationModule\Services\MediaDeviceService;
use App\Modules\WorkstationModule\Services\AgentConnectionService;
use App\Modules\WorkstationModule\Services\HostfileService;
use App\Modules\WorkstationModule\Services\OfflineQueueService;
use App\Modules\WorkstationModule\Services\AccountsService;
use App\Modules\WorkstationModule\Services\PotentialTriggerService;
use App\Modules\WorkstationModule\Models\AgentConnection;
use App\Modules\WorkstationModule\Models\OnlineStats;

use App\Modules\WorkstationModule\Services\CommandLineService;
use App\Modules\WorkstationModule\Models\WebCMD;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // For Services
        $this -> app -> bind( 'WorkstationService', function( $app ){
            return new WorkstationService;
        });

        $this -> app -> bind( 'EventLogService', function( $app ){
            return new EventLogService( app('filesystem') );
        });

        $this -> app -> bind( 'MonitoringService', function( $app ){
            return new MonitoringService;
        });

        $this -> app -> bind( 'MediaDeviceService', function( $app ) {
            return new MediaDeviceService;
        });

        $this -> app -> bind( 'AgentConnectionService', function( $app ){
            return new AgentConnectionService(new AgentConnection, new OnlineStats );
        });

        $this -> app -> bind( 'HostfileService', function( $app ) {
            return new HostfileService( app('filesystem') );
        });

        $this -> app -> bind( 'OfflineQueueService', function($app){
            return new OfflineQueueService;
        });

        $this -> app -> bind( 'AccountsService', function( $app ){
            return new AccountsService;
        });

        $this -> app -> bind( 'PotentialTriggerService', function( $app ){
            return new PotentialTriggerService;
        });

        $this -> app -> bind( 'CommandLineService', function( $app ) {
            return new CommandLineService( new WebCMD );
        });
    }
}
