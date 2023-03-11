<?php

namespace App\Modules\Applications\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Container\Container;
use Laravel\Lumen\Routing\Controller as BaseController;

class UpdateNotification extends BaseController {

    /** @var App\Modules\Applications\Services\NotificationService  $notificationService description */
    protected $notificationService;
    /**
     * Constructor Method
     * Define constructor dependencies here
     *
     * @return void
     **/
    public function __construct(Container $container ){
        $this -> notificationService = $container -> get ('NotificationService');
    }


    /**
     * Handles the Code list
     *
     * @param Request $request
     * @return Response
     * @throws conditon
     **/
    public function __invoke( Request $request )
    {
        $data   = $request->input();
        $data   = $this -> notificationService -> updateNotification($data);
        return ['status' => 'OK', 'data' => $data];
    }
}
