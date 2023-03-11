<?php

$router -> get('/flags', '\App\Modules\Flags\Controllers\FlagListController');
$router -> post('/flags', '\App\Modules\Flags\Controllers\SaveFlagController');

$router -> group(['middleware' => ['secure-header']], function () use($router) {
    $router -> get('/flags/overview', '\App\Modules\Flags\Controllers\OverviewController');
    $router -> get('/flags/breakdown', '\App\Modules\Flags\Controllers\CountryBreakdownController');
    $router -> get('/flags/report/summary', '\App\Modules\Flags\Controllers\GenerateSummaryReportController');
});

// middleware for audit logs
$router -> group(['middleware' => ['audit-logs']], function () use($router) {
    $router -> post('/flags/{id}/update-status', ['as' => 'status', 'uses' => '\App\Modules\Flags\Controllers\UpdateStatusController']);
    $router -> post('/flags/batch-update-status', ['as' => 'batch-status', 'uses' => '\App\Modules\Flags\Controllers\BatchUpdateStatusController']);
    $router -> post('/flags/{id}/workstation-profile/send-request', ['as' => 'send-request', 'uses' => '\App\Modules\Flags\Controllers\WorkstationProfileRequestController']);
});

// $router -> post('/flags/{id}/update-status', '\App\Modules\Flags\Controllers\UpdateStatusController');
// $router -> post('/flags/batch-update-status', '\App\Modules\Flags\Controllers\BatchUpdateStatusController');
$router -> get('/flags/filters', '\App\Modules\Flags\Controllers\FilterController');

$router -> get('/flags/{id}/workstation-profile', '\App\Modules\Flags\Controllers\WorkstationProfileController');
// $router -> post('/flags/{id}/workstation-profile/send-request', '\App\Modules\Flags\Controllers\WorkstationProfileRequestController');
$router -> get('/flags/{worker_id}/workstation-profile/progress', '\App\Modules\Flags\Controllers\WorkstationProfileProgressController');
