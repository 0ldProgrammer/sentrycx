<?php

namespace App\Modules\Flags\Providers;

use Illuminate\Support\ServiceProvider;
use App\Modules\Flags\Services\FlagService;
use App\Modules\Flags\Models\Flag;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this -> app -> bind( 'FlagService', function( $app ){
            return new FlagService( new Flag );
        });
    }
}
