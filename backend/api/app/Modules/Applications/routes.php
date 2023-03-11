<?php

$router -> get('networks/{account_id}',         '\App\Modules\Applications\Controllers\NetworkController');
$router -> get('workday-account/{account}',     '\App\Modules\Applications\Controllers\AccountController');
$router -> get('getIPURL/{value}/{table}/{account}',      '\App\Modules\Applications\Controllers\GetIpController');

$router -> get('trigger/logs', '\App\Modules\Applications\Controllers\PsTriggerLogsController@triggerLogsList');



$router -> group(['middleware' => ['token-header']], function () use($router) {


    $router -> get('/applications/reset-counter',     '\App\Modules\Maintenance\Controllers\ApplicationUrlListController@resetUrlCounter');
    $router -> post('/applications/url-count/{account_id}',     '\App\Modules\Maintenance\Controllers\ApplicationUrlListController@urlCounter');
    $router -> get('/applications/url-list/{account_id}',     '\App\Modules\Maintenance\Controllers\ApplicationUrlListController@forDesktopList');
    
    $router -> get('pending-items/{worker_id}',     '\App\Modules\Applications\Controllers\PendingFlagsController');
	
    $router -> get('applications/desktop-config',    '\App\Modules\Applications\Controllers\DesktopConfigController');
    $router -> post('applications/workstation',     '\App\Modules\Applications\Controllers\SubmitWorkstationController');
    $router -> get('applications/{account_id}',     '\App\Modules\Applications\Controllers\ListController');

    ///desktop notifcations

    $router -> get('applications/desktop-config/',    '\App\Modules\Applications\Controllers\DesktopConfigController');
    $router -> get('notification/{worker_id}',      '\App\Modules\Applications\Controllers\GetNotificationController');
    $router -> get('categories/{account}',          '\App\Modules\Applications\Controllers\CategoryController');
    $router -> get('codes/{account}/{id}',          '\App\Modules\Applications\Controllers\CodeController');
    $router -> get('workday-profile/{username}',    '\App\Modules\Applications\Controllers\WorkdayController');
    $router -> get('start-up-checker/{account}',    '\App\Modules\Applications\Controllers\StartUpCheckerController');
    $router -> get('flag-status/{worker_id}',       '\App\Modules\Applications\Controllers\FlagStatusCheckerController');

    $router -> post('notification/update/',         '\App\Modules\Applications\Controllers\UpdateNotification');
    $router -> post('notification/add',             '\App\Modules\Applications\Controllers\AddNotificationController');
    $router -> post('closeFlag',                    '\App\Modules\Applications\Controllers\CloseFlagController');
    $router -> post('submitCode',                   '\App\Modules\Applications\Controllers\SubmitCodeController');
    $router -> post('submitHardwareInfo',           '\App\Modules\Applications\Controllers\SubmitHardwareInfoController');
    $router -> post('device-status',    '\App\Modules\Applications\Controllers\DeviceStatusController');
    $router -> get('records/unlisted',    '\App\Modules\Applications\Controllers\UnlistedAgentsController');
    $router -> get('device-status',    '\App\Modules\Applications\Controllers\DeviceStatusController');
    $router -> post('records/postResponse',    '\App\Modules\Applications\Controllers\UnlistedAgentsController@postResponse');

    $router -> post('trigger/logs', '\App\Modules\Applications\Controllers\PsTriggerLogsController');

});

// comment this out or remove once redis is initialized in the application list
$router -> get('applications/url-app/initialize',     '\App\Modules\Maintenance\Controllers\ApplicationUrlListController@initializeRedisData');

$router -> post('applications/new-session-id', '\App\Modules\Applications\Controllers\UpdateSessionIdController');
