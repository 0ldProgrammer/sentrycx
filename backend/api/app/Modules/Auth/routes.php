<?php

$router -> group(['middleware' => ['secure-header']], function () use($router) {
    $router -> get('/auth/validate', '\App\Modules\Auth\Controllers\ValidateController');
});



$router -> get('/auth/auth-url', '\App\Modules\Auth\Controllers\AuthenticateController');
$router -> get('/auth/desktop-app-download', '\App\Modules\Auth\Controllers\DesktopAppDownloadController');