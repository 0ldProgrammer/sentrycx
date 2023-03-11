<?php

// middleware for audit logs
$router -> group(['middleware' => ['audit-logs']], function () use($router) {
    $router -> post('/zoho/remote-session', ['as' => 'zoho', 'uses' => '\App\Modules\Zoho\Controllers\SessionController']);
});

$router -> get('/zoho/oauth', '\App\Modules\Zoho\Controllers\OAuthController');
$router -> post('/zoho/access-token', '\App\Modules\Zoho\Controllers\AccessTokenController');
// $router -> post('/zoho/remote-session', '\App\Modules\Zoho\Controllers\SessionController');
$router -> get('/zoho/sessions/{worker_id}','\App\Modules\Zoho\Controllers\ReportController');
