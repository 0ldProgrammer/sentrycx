<?php 

namespace App\Modules\Reports\Providers;

use App\Modules\Reports\Services\ConnectionReportService;
use Illuminate\Support\ServiceProvider;
use App\Modules\Reports\Services\ReportService;

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
        $this -> app -> bind( 'ReportService', function( $app ){
            return new ReportService;
        });

        $this -> app -> bind( 'ConnectionReportService', function( $app ){
            return new ConnectionReportService;
        });
    }
}