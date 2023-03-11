<?php 

$router -> group(['middleware' => ['secure-header']], function () use($router) {
    $router -> get('/records/timestamps', '\App\Modules\HistoricalRecords\Controllers\PingTimestampsListController');
    $router -> get('/records/applications', '\App\Modules\HistoricalRecords\Controllers\PingApplicationsListController');
});

$router -> group(['middleware' => ['token-header']], function () use($router) {

    $router -> post('/records/{worker_id}/mean-opinion-score', '\App\Modules\HistoricalRecords\Controllers\MeanOpinionScoreSaveController');
    $router -> post('/records/{worker_id}/workstation-profile', '\App\Modules\HistoricalRecords\Controllers\WorkstationProfileSaveController');
    $router -> post('/records/workstation-trace', '\App\Modules\HistoricalRecords\Controllers\WorkstationTraceSaveController');
});

$router -> get('/records/{worker_id}/mean-opinion-score', '\App\Modules\HistoricalRecords\Controllers\MeanOpinionScoreController');

$router -> get('/records/{worker_id}/workstation-profile', '\App\Modules\HistoricalRecords\Controllers\WorkstationProfileController');

$router -> get('/records/{worker_id}/speedtest', '\App\Modules\HistoricalRecords\Controllers\SpeedtestController');

// audit logs
$router -> get('/records/audit', '\App\Modules\HistoricalRecords\Controllers\AuditLogsController');
$router -> get('/records/search-audit-logs', '\App\Modules\HistoricalRecords\Controllers\AuditLogsSearchController');
$router -> get('/records/audit-logs/filters', '\App\Modules\HistoricalRecords\Controllers\AuditLogsFilterController');

//  securecx
$router -> get('/records/{worker_id}/securecx-monitoring', '\App\Modules\HistoricalRecords\Controllers\SecureCXMonitoringController');
$router -> get('/records/securecx-urls', '\App\Modules\HistoricalRecords\Controllers\SecureCXUrlsController');


$router -> get('/records/ping/report', '\App\Modules\HistoricalRecords\Controllers\PingReportController');
$router -> get('/records/trace/report', '\App\Modules\HistoricalRecords\Controllers\TraceReportController');
$router -> get('/records/mos/report', '\App\Modules\HistoricalRecords\Controllers\MOSReportController');
$router -> get('/records/speedtest/report', '\App\Modules\HistoricalRecords\Controllers\SpeedtestReportController');
$router -> get('/records/workstation-history/report', '\App\Modules\HistoricalRecords\Controllers\WorkstationHistoryReportController');

$router -> get('/records/one-view-graph', '\App\Modules\HistoricalRecords\Controllers\LogsReportController');