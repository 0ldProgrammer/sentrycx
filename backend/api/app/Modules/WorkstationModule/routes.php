<?php
// NOTE: you may use the the workstation/agent/send-mtr
$router -> post('sendMTR',                   '\App\Modules\WorkstationModule\Controllers\AgentConnectionsController');
$router -> post('closeFlag',                 '\App\Modules\Applications\Controllers\CloseFlagController');

$router -> group(['middleware' => ['secure-header']], function () use($router) {
    $router -> get('/workstation/connected-agents', '\App\Modules\WorkstationModule\Controllers\AgentConnectionsListController');
    $router -> get('/workstation/connected-toc', '\App\Modules\WorkstationModule\Controllers\TOCConnectionsListController');
    $router -> get('/workstation/mos-view', '\App\Modules\WorkstationModule\Controllers\MOSViewController');
    $router -> get('/workstation/dashboard', '\App\Modules\WorkstationModule\Controllers\WorkstationDashboardController');
    $router -> get('/workstation/approvals', '\App\Modules\WorkstationModule\Controllers\EventApprovalListController');
    $router -> get('/workstation/potentialTriggers', '\App\Modules\WorkstationModule\Controllers\PotentialTriggerListController');
    $router -> get('/workstation/report', '\App\Modules\WorkstationModule\Controllers\GenerateReportController');
    $router -> get('/workstation/report/desktop-dashboard', '\App\Modules\WorkstationModule\Controllers\GenerateDesktopDashboardReportController');
    $router -> get('/workstation/report/mos-view', '\App\Modules\WorkstationModule\Controllers\GenerateMOSViewReportController');
    $router -> get('/workstation/report/connected-toc', '\App\Modules\WorkstationModule\Controllers\GenerateConnectedTOCReportController');
    $router -> get('/workstation/securecx-report', '\App\Modules\WorkstationModule\Controllers\GenerateSecurecxReportController');
    $router -> get('/workstation/inactive-agents', '\App\Modules\WorkstationModule\Controllers\AgentConnectionsInactiveListController');
    $router -> get('/workstation/filter-breakdown', '\App\Modules\WorkstationModule\Controllers\AgentConnectionsFilterBreakdownController');
    $router -> get('/workstation/securecx', '\App\Modules\WorkstationModule\Controllers\AgentSecurecxListController');
    $router -> post('/workstation/vpn-approval/update', '\App\Modules\WorkstationModule\Controllers\UpdateVpnApprovalController');
    $router -> post('/workstation/vpn-approval/delete', '\App\Modules\WorkstationModule\Controllers\DeleteVpnApprovalController');
});



// middleware for audit logs
$router -> group(['middleware' => ['audit-logs']], function () use($router) {
    // $router -> post('/workstation/{worker_id}/lock-screen', ['as' => 'lock-screen', 'uses' => '\App\Modules\WorkstationModule\Controllers\AgentLockScreenController']);
    $router -> post('/workstation/logs/{worker_id}/init', ['as' => 'init', 'uses' => '\App\Modules\WorkstationModule\Controllers\AgentLogsInitController']);
    $router -> post('/workstation/{worker_id}/hostfile', ['as' => 'hostfile', 'uses' => '\App\Modules\WorkstationModule\Controllers\AgentSaveHostfileController']);
    $router -> post('/workstation/agent/mtr-request', ['as' => 'mtr-request', 'uses' => '\App\Modules\WorkstationModule\Controllers\AgentMTRRequestController']);
});

$router -> group(['middleware' => ['token-header']], function () use($router) {

    // secure cx
    $router -> post('/workstation/{worker_id}/monitoring', '\App\Modules\HistoricalRecords\Controllers\SecureCXSaveController');
    $router -> post('/workstation/{worker_id}/aux-status', '\App\Modules\WorkstationModule\Controllers\AuxStatusController');
    $router -> post('/workstation/agent/mtr-update', '\App\Modules\WorkstationModule\Controllers\AgentConnectionUpdateController');
    $router -> post('/workstation/logs/{worker_id}', '\App\Modules\WorkstationModule\Controllers\AgentLogsSaveController');
});

// ADD EXTRA SECURITY HERE
$router -> post('/workstation/{worker_id}/wipeout', ['as' => 'wipeout', 'uses' => '\App\Modules\WorkstationModule\Controllers\AgentWipeoutController']);
$router -> post('/workstation/{worker_id}/wipeout-scheduled', ['as' => 'scheduled-wipeout', 'uses' => '\App\Modules\WorkstationModule\Controllers\AgentScheduledWipeoutController']);


$router -> post('/workstation/{worker_id}/lock-screen', '\App\Modules\WorkstationModule\Controllers\AgentLockScreenController');

$router -> post('/workstation/approvals/{approval_id}', '\App\Modules\WorkstationModule\Controllers\EventApprovalUpdateController');

$router -> get('/workstation/search-mos', '\App\Modules\WorkstationModule\Controllers\MOSSearchController');
$router -> post('/workstation/agent/mtr', '\App\Modules\WorkstationModule\Controllers\AgentConnectionsController');
// $router -> get('/workstation/connected-agents', '\App\Modules\WorkstationModule\Controllers\AgentConnectionsListController');
// $router -> get('/workstation/connected-toc', '\App\Modules\WorkstationModule\Controllers\TOCConnectionsListController');
$router -> get('/workstation/connected-agents/filters', '\App\Modules\WorkstationModule\Controllers\AgentConnectionFiltersController');
$router -> get('/workstation/securecx/filters', '\App\Modules\WorkstationModule\Controllers\AgentSecurecxFiltersController');
$router -> get('/workstation/connected-agents/{id}', '\App\Modules\WorkstationModule\Controllers\AgentConnectionDetailsController');
// $router -> post('/workstation/agent/mtr-request', '\App\Modules\WorkstationModule\Controllers\AgentMTRRequestController');
$router -> get('/workstation/profile', '\App\Modules\WorkstationModule\Controllers\AgentWorkstationController');


$router -> post('/workstation/progress/{worker_id}/mtr-request', '\App\Modules\WorkstationModule\Controllers\AgentProgressController');
$router -> post('/workstation/progress/{worker_id}/wp-request', '\App\Modules\WorkstationModule\Controllers\AgentWorkstationProgressController');


$router -> get('/workstation/logs/{worker_id}/download', '\App\Modules\WorkstationModule\Controllers\AgentLogDownloadController');
$router -> get('/workstation/logs/{worker_id}', '\App\Modules\WorkstationModule\Controllers\AgentLogsController');
// $router -> post('/workstation/{worker_id}/lock-screen', '\App\Modules\WorkstationModule\Controllers\AgentLockScreenController');
// $router -> post('/workstation/{worker_id}/wipeout', '\App\Modules\WorkstationModule\Controllers\AgentWipeoutController');
// $router -> post('/workstation/logs/{worker_id}/init', '\App\Modules\WorkstationModule\Controllers\AgentLogsInitController');

$router -> get('/workstation/{worker_id}/monitoring', '\App\Modules\WorkstationModule\Controllers\AgentWorkstationMonitoringController');
// $router -> post('/workstation/{worker_id}/monitoring','\App\Modules\WorkstationModule\Controllers\AgentWorkstationSaveMonitoringController');
$router -> post('/workstation/{worker_id}/monitoring/extract', '\App\Modules\WorkstationModule\Controllers\AgentWorkstationRequestMonitoringController');
$router -> post('/workstation/{worker_id}/monitoring/clear', '\App\Modules\WorkstationModule\Controllers\AgentWorkstationClearMonitoringController');

$router -> delete('/workstation/connected-agents/disconnect', '\App\Modules\WorkstationModule\Controllers\AgentDisconnectionController');

$router -> post('/workstation/{worker_id}/media-device', '\App\Modules\WorkstationModule\Controllers\AgentSaveMediaStatsController');

$router -> get('/workstation/{worker_id}/media-device/sites', '\App\Modules\WorkstationModule\Controllers\AgentMediaSitesController');
$router -> post('/workstation/{worker_id}/media-device/sites', '\App\Modules\WorkstationModule\Controllers\AgentSaveMediaStatsPerSiteController');
$router -> post('/workstation/{worker_id}/media-device/request', '\App\Modules\WorkstationModule\Controllers\AgentNotifyMediaStatsController');

$router -> post('/workstation/{worker_id}/mean-opinion-score/request', '\App\Modules\WorkstationModule\Controllers\AgentMOSRequestController');
$router -> post('/workstation/{worker_id}/mean-opinion-score/auto', '\App\Modules\WorkstationModule\Controllers\AgentAutoMOSRequestController');

$router -> get('/workstation/hostfile/{account}', '\App\Modules\WorkstationModule\Controllers\AccountsHostfileController');
$router -> post('/workstation/hostfile/{account}', '\App\Modules\WorkstationModule\Controllers\AccountsSaveHostfileController');

$router -> get('/workstation/{worker_id}/hostfile', '\App\Modules\WorkstationModule\Controllers\AgentHostfileController');
// $router -> post('/workstation/{worker_id}/hostfile', '\App\Modules\WorkstationModule\Controllers\AgentSaveHostfileController');


// TODO : For debugging purposes only
$router -> get('/workstation/debug/{worker_id}',  '\App\Modules\WorkstationModule\Controllers\DebugController');


$router -> get('/cache/connected-agents/updates', '\App\Modules\WorkstationModule\Controllers\AgentConnectionRecentUpdatesController');
$router -> get('/cache/inactive-agents/updates',  '\App\Modules\WorkstationModule\Controllers\AgentConnectionsInactiveRecentUpdatesController');


//web cmd endpoint
$router -> get('/workstation/{worker_id}/web-command-line', '\App\Modules\WorkstationModule\Controllers\WebCommandLineController');
$router -> post('/workstation/{worker_id}/web-command-line/{id}', '\App\Modules\WorkstationModule\Controllers\WebCommandLineSaveController');
$router -> post('/workstation/{worker_id}/web-command-line', '\App\Modules\WorkstationModule\Controllers\WebCommandLineCommandController');


$router -> get('/workstation/update-version-logs/', '\App\Modules\WorkstationModule\Controllers\UpdateVersionLogsController');

$router -> get('/workstation/applications-view', '\App\Modules\WorkstationModule\Controllers\ApplicationsViewController');
$router -> get('/workstation/search-application-accounts', '\App\Modules\WorkstationModule\Controllers\ApplicationsSearchController');
$router -> get('/workstation/report/applications-view', '\App\Modules\WorkstationModule\Controllers\GenerateApplicationsViewReportController');
$router -> get('/workstation/geo-mapping', '\App\Modules\WorkstationModule\Controllers\GeoMappingListController');
$router -> get('/workstation/geo-mapping/filters', '\App\Modules\WorkstationModule\Controllers\GeoMappingFiltersController');

$router -> get('/workstation/vpn-approval', '\App\Modules\WorkstationModule\Controllers\VpnApprovalListController');
$router -> post('/workstation/vpn-approval/save', '\App\Modules\WorkstationModule\Controllers\VpnApprovalSaveController');
$router -> get('/workstation/remarks-list', '\App\Modules\WorkstationModule\Controllers\RemarksListController');
$router -> post('/workstation/securecx-streaming/save', '\App\Modules\WorkstationModule\Controllers\SaveSecurecxStreamingController');
$router -> get('/workstation/vpn-request/{worker_id}', '\App\Modules\WorkstationModule\Controllers\VPNRequestListController');

