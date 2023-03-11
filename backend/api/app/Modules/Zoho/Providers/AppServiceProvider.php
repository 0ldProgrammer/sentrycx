<?php

namespace App\Modules\Zoho\Providers;

use Illuminate\Support\ServiceProvider;
use App\Modules\Zoho\Services\ZohoService;
use App\Modules\Zoho\Services\ZohoLogService;

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
        $this -> app -> bind( 'ZohoService', function( $app ){
            return new ZohoService(
                getenv('ZOHO_CLIENT_ID'),
                getenv('ZOHO_SECRET'),
                getenv('ZOHO_REDIRECT_URI'),
                getenv('ZOHO_REFRESH_TOKEN')
            );
        });

        $this -> app -> bind( 'ZohoReportAPI', function( $app ){
            return new ZohoService(
                getenv('ZOHO_REPORT_CLIENT_ID'),
                getenv('ZOHO_REPORT_SECRET'),
                getenv('ZOHO_REDIRECT_URI'),
                getenv('ZOHO_REPORT_REFRESH_TOKEN')
            );
        });

        $this -> app -> bind( 'ZohoLogService', function( $app ){
            return new ZohoLogService;
        });
    }
}
