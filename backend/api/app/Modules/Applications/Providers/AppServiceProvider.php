<?php

namespace App\Modules\Applications\Providers;

use Illuminate\Support\ServiceProvider;
use App\Modules\Applications\Services\ApplicationService;
use App\Modules\Applications\Services\DeviceStatusService;
use App\Modules\Applications\Services\NotificationService;
use App\Modules\Flags\Helpers\FlagValidator;
use App\Modules\HistoricalRecords\Services\MeanOpinionScoreRecordsService;
use App\Modules\HistoricalRecords\Services\SpeedtestRecordsService;
use App\Modules\HistoricalRecords\Services\WorkstationProfileRecordsService;
use App\Modules\Applications\Services\PsTriggerLogsService;

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
        $this -> app -> bind( 'ApplicationService', function( $app ){
            return new ApplicationService( 
                new SpeedtestRecordsService, 
                new WorkstationProfileRecordsService,
                new MeanOpinionScoreRecordsService
             );
        });

        $this -> app -> bind( 'NotificationService', function( $app ){
            return new NotificationService;
        });

        $this -> app -> bind( 'DeviceStatusService', function( $app ){
            return new DeviceStatusService;
        });

        $this -> app -> bind( 'PsTriggerLogsService', function( $app ){
            return new PsTriggerLogsService;
        });

        // For Facades
        $this -> app -> bind('flag-validator', function($app){
            return new FlagValidator;
        });
    }
}
