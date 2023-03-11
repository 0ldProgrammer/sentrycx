<?php 


// $router -> get('/workday/profile/{worker_id}', function( $worker_id ){
//     return ['status' => 'OK'];
// });
$router -> get('/workday/profile/{worker_id}', '\App\Modules\Workday\Controllers\ProfileController');

// $router -> post('/workstation/hostfile/{account}', '\App\Modules\WorkstationModule\Controllers\AccountsSaveHostfileController');

$router -> get('/records/invalid-usernames', '\App\Modules\Workday\Controllers\InvalidUsernamesController');
$router -> get('/records/search-invalid-usernames', '\App\Modules\Workday\Controllers\InvalidUsernamesSearchController');
$router -> get('/test', '\App\Modules\Workday\Controllers\TestController');