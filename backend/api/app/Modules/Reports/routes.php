<?php 
 
$router -> get('reports/interval-summary','\App\Modules\Reports\Controllers\IntervalSummaryController') ;
$router -> get('reports/interval-detail','\App\Modules\Reports\Controllers\IntervalDetailController') ;

// for disable
$router -> get('reports/disable', ['as' => 'disabled','uses'=> '\App\Modules\Reports\Controllers\DisableReportTypeController']);
$router -> post('reports/update-email-notifications', '\App\Modules\Reports\Controllers\UpdateEmailNotificationsController');

$router -> post('reports/details','\App\Modules\Reports\Controllers\ReportDetailsController') ;
$router -> get('reports/location/{account}','\App\Modules\Reports\Controllers\ReportDetailsController@getLocationByAccount') ;
$router -> get('reports/type','\App\Modules\Reports\Controllers\ReportDetailsController@getReportType') ;
$router -> get('reports/threshold/{report_id}','\App\Modules\Reports\Controllers\ReportDetailsController@getThreshold') ;
$router -> get('reports/threshold/{report_id}/{account}','\App\Modules\Reports\Controllers\ReportDetailsController@getThreshold') ;

