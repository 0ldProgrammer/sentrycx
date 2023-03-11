<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(\App\Services\UrlGenerator::class, function () {
            $urlGeneratorWithSignedRoutes = new \App\Services\UrlGenerator($this->app);
        
            $urlGeneratorWithSignedRoutes->setKeyResolver(function () {
                return $this->app->make('config')->get('app.key');
            });
        
            return $urlGeneratorWithSignedRoutes;
        });
    }

}
