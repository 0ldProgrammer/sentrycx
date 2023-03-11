<?php

namespace App\Modules\Auth\Providers;

use Illuminate\Support\ServiceProvider;
use App\Modules\Auth\Services\AuthService;
use App\Modules\Auth\Services\InstallerService;
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
        $this -> app -> bind( 'AuthService', function( $app ){
            return new AuthService( new Client() );
        });

        $this -> app -> bind( 'InstallerService', function( $app ){
            return new InstallerService( app('filesystem') );
        });
    }
}
