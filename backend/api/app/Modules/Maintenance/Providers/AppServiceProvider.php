<?php

namespace App\Modules\Maintenance\Providers;

use Illuminate\Support\ServiceProvider;
use App\Modules\Maintenance\Services\MaintenanceService;
use App\Modules\Maintenance\Services\OrganizationService;
use App\Modules\Maintenance\Services\UserService;
use App\Modules\Maintenance\Services\UserConfigService;

use \GuzzleHttp\Client;

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
        $this -> app -> bind( 'MaintenanceService', function( $app ){
            return new MaintenanceService( new Client() );
        });

        $this -> app -> bind('UserService', function( $app ){
            return new UserService;
        });

        $this -> app -> bind('OrganizationService', function( $app ){
            return new OrganizationService;
        });

        $this -> app -> bind('UserConfigService', function( $app ){
            return new UserConfigService;
        });
    }
}
