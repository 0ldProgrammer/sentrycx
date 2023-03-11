<?php
namespace App\Modules\Maintenance\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Container\Container;
use App\Http\Controllers\Controller;


class UpdateMailNotificationsController extends Controller {

    /** @var App\Modules\Maintenance\Services\MaintenanceService  $maintenanceService description */
    protected $maintenanceService;
    /**
     * Constructor Method
     * Define constructor dependencies here
     *
     * @return void
     **/
    public function __construct(Container $container ){
        $this -> maintenanceService = $container -> get ('MaintenanceService');
    }
    

    /**
     * Handles the updates of mail notification
     *
     * @param Request $request
     * @return Response
     * @throws conditon
     **/
    public function __invoke( Request $request)
    {
        $data = $request->input();

        $this -> maintenanceService -> updateMailNotifications($data);

        return ['status' => 'OK'];
    }

}
