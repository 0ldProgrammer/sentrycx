<?php
//  phpinfo();
#$check = extension_loaded('redis');

#die("TEST $check");
require_once __DIR__.'/../vendor/autoload.php';

// custom external directory libraries
require_once __DIR__.'/../../../migration/libraries/vendor/autoload.php';

(new Laravel\Lumen\Bootstrap\LoadEnvironmentVariables(
    dirname(__DIR__)
))->bootstrap();

date_default_timezone_set(env('APP_TIMEZONE', 'UTC'));

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
*/

$app = new Laravel\Lumen\Application(
    dirname(__DIR__)
);

$app->withFacades();

$app->withEloquent();

/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
*/

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

// $app->singleton('filesystem', function ($app) {
//     return $app->loadComponent('filesystems', 'Illuminate\Filesystem\FilesystemServiceProvider', 'filesystem');
// });

/*
|--------------------------------------------------------------------------
| Register Config Files
|--------------------------------------------------------------------------
|
| Now we will register the "app" configuration file. If the file exists in
| your configuration directory it will be loaded; otherwise, we'll load
| the default version. You may register other files below as needed.
|
*/
if (!function_exists('public_path')) {
    /**
     * Return the path to public dir
     *
     * @param null $path
     *
     * @return string
     */
    function public_path($path = null)
    {
        return rtrim(app()->basePath('public/' . $path), '/');
    }
}

$app->configure('app');
$app->configure('mail');
$app->configure('filesystems');
$app->alias('mailer', Illuminate\Mail\Mailer::class);
$app->alias('mailer', Illuminate\Contracts\Mail\Mailer::class);
$app->alias('mailer', Illuminate\Contracts\Mail\MailQueue::class);

// class_alias('Illuminate\Support\Facades\Storage', 'Storage');
/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
*/

$app->middleware([
	App\Http\Middleware\CorsMiddleware::class,
	App\Http\Middleware\BeforeMiddleware::class
//     App\Http\Middleware\ExampleMiddleware::class
]);

$app->routeMiddleware([
    'secure-header' => App\Http\Middleware\SecureHeaderMiddleware::class,
    'audit-logs' => App\Http\Middleware\AuditLogsMiddleware::class,
    'token-header' => App\Http\Middleware\TokenHeaderMiddleware::class
    // 'auth' => App\Http\Middleware\Authenticate::class,
]);

/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
*/
$app -> register(App\Providers\AppServiceProvider::class);
$app -> register(App\Modules\Applications\Providers\AppServiceProvider::class);

$app -> register(App\Modules\Flags\Providers\AppServiceProvider::class);
$app -> register(App\Modules\WorkstationModule\Providers\AppServiceProvider::class);
$app -> register(App\Modules\Auth\Providers\AppServiceProvider::class);
$app -> register(App\Modules\Maintenance\Providers\AppServiceProvider::class);
$app -> register(App\Modules\Zoho\Providers\AppServiceProvider::class);
$app -> register(App\Modules\Workday\Providers\AppServiceProvider::class);
$app -> register(App\Modules\HistoricalRecords\Providers\AppServiceProvider::class);
$app -> register(App\Modules\Reports\Providers\AppServiceProvider::class);

$app -> register(Illuminate\Redis\RedisServiceProvider::class);

$app -> register(Illuminate\Mail\MailServiceProvider::class);

// $app -> register(TillKruss\LaravelPhpRedis\RedisServiceProvider::class);
$app->register(Flipbox\LumenGenerator\LumenGeneratorServiceProvider::class);;
if (!class_exists('Redis')) {
    class_alias('Illuminate\Support\Facades\Redis', 'Redis');
}
// $app->register(App\Providers\AppServiceProvider::class);
// $app->register(App\Providers\AuthServiceProvider::class);
// $app->register(App\Providers\EventServiceProvider::class);
$app->register(Illuminate\Filesystem\FilesystemServiceProvider::class);

/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
|
| Next we will include the routes file so that they can all be added to
| the application. This will provide all of the URLs the application
| can respond to, as well as the controllers that may handle them.
|
*/

$app->router->group([
    'namespace' => 'App\Http\Controllers',
], function ($router) {
    require __DIR__.'/../routes/web.php';
});

return $app;
