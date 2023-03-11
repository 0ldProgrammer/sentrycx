<?php
$router -> group(['middleware' => ['secure-header']], function () use($router) {
    $router -> get('maintenance/users', '\App\Modules\Maintenance\Controllers\UserListController');
    $router -> get('maintenance/msa', '\App\Modules\Maintenance\Controllers\MSAListController');
    $router -> get('/maintenance/accounts/query', '\App\Modules\Maintenance\Controllers\AccountsListController');
    $router -> get('maintenance/codes', '\App\Modules\Maintenance\Controllers\CodeListController');
    $router -> get('/maintenance/programme-msa', '\App\Modules\Maintenance\Controllers\UserMSAListController');
    $router -> get('/maintenance/subnet-mapping', '\App\Modules\Maintenance\Controllers\SubnetMappingListController');
    $router -> get('/maintenance/vlan-mapping', '\App\Modules\Maintenance\Controllers\VlanMappingListController');
    $router -> get('/maintenance/client-location-list', '\App\Modules\Maintenance\Controllers\UserDistinctController');
});


$router -> get('/maintenance/desktop-application-list', '\App\Modules\Maintenance\Controllers\DesktopApplicationListController');

$router -> post('/maintenance/vlan/add-mapping', '\App\Modules\Maintenance\Controllers\VlanMappingAddController');
$router -> post('/maintenance/vlan/edit-mapping', '\App\Modules\Maintenance\Controllers\VlanMappingEditController');
$router -> post('/maintenance/vlan/delete-mapping', '\App\Modules\Maintenance\Controllers\VlanMappingDeleteController');

$router -> post('/maintenance/add-mapping', '\App\Modules\Maintenance\Controllers\SubnetMappingAddController');
$router -> post('/maintenance/edit-mapping', '\App\Modules\Maintenance\Controllers\SubnetMappingEditController');
$router -> post('/maintenance/delete-mapping', '\App\Modules\Maintenance\Controllers\SubnetMappingDeleteController');

$router -> get('maintenance/accounts',                      '\App\Modules\Maintenance\Controllers\AccountController');
$router -> post('maintenance/accounts/codes-assignment',    '\App\Modules\Maintenance\Controllers\CodeAssignDeleteController');
$router -> post('maintenance/accounts/add-code',            '\App\Modules\Maintenance\Controllers\CodeAddController');
$router -> post('maintenance/accounts/delete-code',         '\App\Modules\Maintenance\Controllers\CodeDeleteController');

// $router -> get('/maintenance/codes', '\App\Modules\Maintenace\Controllers\CodeController');

// $router -> get('maintenance/users', '\App\Modules\Maintenance\Controllers\UserListController');
$router -> get('maintenance/user/{id}', '\App\Modules\Maintenance\Controllers\UserDetailsController');
$router -> get('maintenance/search-users', '\App\Modules\Maintenance\Controllers\UserSearchController');
$router -> post('maintenance/user', '\App\Modules\Maintenance\Controllers\UserAddController');
$router -> post('maintenance/user/{id}', '\App\Modules\Maintenance\Controllers\UserUpdateController');
$router -> delete('maintenance/user/{id}', '\App\Modules\Maintenance\Controllers\UserDeleteController');

$router -> get('maintenance/user/{id}/config', '\App\Modules\Maintenance\Controllers\UserConfigController');
$router -> post('maintenance/user/{id}/config', '\App\Modules\Maintenance\Controllers\UserConfigSaveController');

$router -> get('users/filters', '\App\Modules\Maintenance\Controllers\UserFilterController');

$router -> get('maintenance/users/sites-available', '\App\Modules\Maintenance\Controllers\UsersSitesAvailableController');

$router -> get('maintenance/users/accounts-available', '\App\Modules\Maintenance\Controllers\UsersAccountsAvailableController');

// TODO : already have maintenance/accounts but that endpoint doesn't have paginate

$router -> post('/maintenance/accounts', '\App\Modules\Maintenance\Controllers\AccountsAddController');
$router -> post('/maintenance/accounts/{id}', '\App\Modules\Maintenance\Controllers\AccountsUpdateController');
$router -> delete('/maintenance/accounts/{id}', '\App\Modules\Maintenance\Controllers\AccountsDeleteController');

$router -> get('maintenance/search-programme-msa', '\App\Modules\Maintenance\Controllers\UserProgrammeMSASearchController');

$router -> post('/maintenance/msa-users', '\App\Modules\Maintenance\Controllers\UserAddMSAController');
$router -> delete('maintenance/msa/{id}', '\App\Modules\Maintenance\Controllers\UserDeleteMSAController');

$router -> get('maintenance/user-net-type', '\App\Modules\Maintenance\Controllers\UserNetTypeController');

$router -> post('maintenance/aux/add-aux', '\App\Modules\Maintenance\Controllers\AuxAddController');
$router -> get('maintenance/aux', '\App\Modules\Maintenance\Controllers\AuxListController');
$router -> post('maintenance/aux/accounts/delete', '\App\Modules\Maintenance\Controllers\AuxDeleteController');
$router -> post('maintenance/aux/accounts/assignment', '\App\Modules\Maintenance\Controllers\AuxAssignDeleteController');
$router -> get('maintenance/aux/data', '\App\Modules\Maintenance\Controllers\AuxUnpaginatedController');

$router -> get('maintenance/deployment-applications-list', '\App\Modules\Maintenance\Controllers\DeploymentApplicationsListController');
$router -> post('maintenance/application/add-deployment-application', '\App\Modules\Maintenance\Controllers\AddDeploymentApplicationController');
$router -> post('maintenance/application/delete-deployment-application', '\App\Modules\Maintenance\Controllers\DeleteDeploymentApplicationController');
$router -> get('maintenance/applications-list', '\App\Modules\Maintenance\Controllers\ApplicationsListController');
$router -> post('maintenance/add-application', '\App\Modules\Maintenance\Controllers\ApplicationAddController');
$router -> post('maintenance/edit-application', '\App\Modules\Maintenance\Controllers\ApplicationEditController');
$router -> post('maintenance/delete-application', '\App\Modules\Maintenance\Controllers\ApplicationDeleteController');
$router -> get('maintenance/account-list', '\App\Modules\Maintenance\Controllers\UserAccountListController');

$router -> get('maintenance/applications', '\App\Modules\Maintenance\Controllers\ApplicationUrlListController');
$router -> post('maintenance/applications/add', '\App\Modules\Maintenance\Controllers\ApplicationUrlAddController');

$router -> get('maintenance/software-updates', '\App\Modules\Maintenance\Controllers\SoftwareUpdatesListController');
$router -> get('maintenance/software-updates/execute', '\App\Modules\Maintenance\Controllers\SoftwareUpdatesExecuteController');
$router -> post('maintenance/software-update/save', '\App\Modules\Maintenance\Controllers\SoftwareUpdatesSaveController');
$router -> post('maintenance/software-update/result', '\App\Modules\Maintenance\Controllers\SoftwareUpdatesInstallationResultController');
$router -> get('maintenance/install-application/{worker_id}', '\App\Modules\Maintenance\Controllers\InstallApplicationController');

$router -> get('maintenance/mail-notifications', '\App\Modules\Maintenance\Controllers\MailNotificationsListController');
$router -> post('maintenance/update-mail-notifications', '\App\Modules\Maintenance\Controllers\UpdateMailNotificationsController');

$router -> get('maintenance/time-frames', '\App\Modules\Maintenance\Controllers\TimeFramesListController');
$router -> post('maintenance/time-frames/add-time-frame', '\App\Modules\Maintenance\Controllers\TimeFrameAddController');
$router -> post('maintenance/time-frames/edit-time-frame', '\App\Modules\Maintenance\Controllers\TimeFrameEditController');
$router -> post('maintenance/time-frames/delete-time-frame', '\App\Modules\Maintenance\Controllers\TimeFrameDeleteController');
$router -> get('maintenance/time-frames/check-time-frame', '\App\Modules\Maintenance\Controllers\TimeFrameCheckController');

$router -> get('maintenance/theme/{worker_id}/config', '\App\Modules\Maintenance\Controllers\AgentThemeController@getAgentTheme');
$router -> post('maintenance/theme/{worker_id}/config', '\App\Modules\Maintenance\Controllers\AgentThemeController');


