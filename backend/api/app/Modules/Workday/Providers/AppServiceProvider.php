<?php 

namespace App\Modules\Workday\Providers;

use Illuminate\Support\ServiceProvider;
use App\Modules\Workday\Services\EmployeeService;

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
        $this -> app -> bind( 'EmployeeService', function( $app ){
            return new EmployeeService;
        });
    }
}