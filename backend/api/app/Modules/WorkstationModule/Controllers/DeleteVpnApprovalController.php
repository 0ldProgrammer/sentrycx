<?php
namespace App\Modules\WorkstationModule\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Container\Container;
use App\Http\Controllers\Controller;
use App\Modules\WorkstationModule\Services\WorkstationService;

class DeleteVpnApprovalController extends Controller {

    /** @var App\Modules\WorkstationModule\Services\WorkstationService  $workstationService description */
    protected $workstationService = null;
    /**
     * Constructor Method
     * Define constructor dependencies here
     *
     * @return void
     **/
    public function __construct(Container $container ){
        $this -> workstationService = $container -> get ('WorkstationService');
    }
    

    /**
     * Handles the vpn request delete
     *
     * @param Request $request
     * @return Response
     * @throws conditon
     **/
    public function __invoke( Request $request)
    {
        $data = $request->input();
        
        return $this -> workstationService -> deleteVpnApproval($data);
    }
}
