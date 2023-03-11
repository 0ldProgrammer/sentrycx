<?php

namespace App\Modules\HistoricalRecords\Providers;

use Illuminate\Support\ServiceProvider;
use App\Modules\HistoricalRecords\Services\MeanOpinionScoreRecordsService;
use App\Modules\HistoricalRecords\Services\WorkstationProfileRecordsService;
use App\Modules\HistoricalRecords\Services\SpeedtestRecordsService;
use App\Modules\HistoricalRecords\Services\AuditRecordsService;
use App\Modules\HistoricalRecords\Services\SecureCXRecordsService;
use App\Modules\HistoricalRecords\Services\WorkstationTraceRecordsService;
use App\Modules\HistoricalRecords\Services\PingTraceRecordsService;
use App\Modules\HistoricalRecords\Services\LogsRecordsService;

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
        $this -> app -> bind( 'MeanOpinionScoreRecordsService', function( $app ){
            return new MeanOpinionScoreRecordsService;
        });

        $this -> app -> bind( 'WorkstationProfileRecordsService', function( $app ) {
            return new WorkstationProfileRecordsService;
        });

        $this -> app -> bind( 'SpeedtestRecordsService', function( $app ){
            return new SpeedtestRecordsService;
        });

        $this -> app -> bind( 'AuditRecordsService', function( $app ){
            return new AuditRecordsService;
        });

        $this -> app -> bind( 'SecureCXRecordsService', function( $app ){
            return new SecureCXRecordsService;
        });

        $this -> app -> bind( 'WorkstationTraceRecordsService', function( $app ){
            return new WorkstationTraceRecordsService;
        });

        $this -> app -> bind( 'PingTraceRecordsService', function( $app ){
            return new PingTraceRecordsService;
        });

        $this -> app -> bind( 'LogsRecordsService', function( $app ){
            return new LogsRecordsService;
        });
    }
}
