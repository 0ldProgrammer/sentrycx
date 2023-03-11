<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

foreach (scandir($path = base_path('app/Modules')) as $dir) {
    if (file_exists($filepath = "{$path}/{$dir}/routes.php"))
        require $filepath;
}
