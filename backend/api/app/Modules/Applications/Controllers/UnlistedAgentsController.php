<?php

namespace App\Modules\Applications\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Container\Container;
use Laravel\Lumen\Routing\Controller as BaseController ;
use App\Modules\Workday\Services\EmployeeService;

class UnlistedAgentsController extends BaseController {

    /** @var EmployeeService $employeeService description */
    protected $employeeService;

    /** @var App\Modules\Applications\Services\ApplicationService  $applicationService description */
    protected $applicationService;
    /**
     * Constructor Method
     * Define constructor dependencies here
     *
     * @return void
     **/
    public function __construct(Container $container ){
        $this -> employeeService = $container -> get('EmployeeService');
    }


    /**
     * Handles the Application list
     *
     * @param Request $request
     * @return Response
     * @throws conditon
     **/
    public function __invoke( Request $request)
    {
        $unlisted = $this -> employeeService -> fetchUnlisted();

        return response()->json([
            'list'  => $unlisted,
            'total' => count($unlisted)
        ]);
    }

    public function postResponse(Request $request)
    {
        $result = $this -> employeeService -> UpdateOrInsertEmployee($request->input('employeeID'), $request);

        return response()->json([
            'status' => $result['status'],
            'response' => $result['msg'],
            'employee_id' => $request->input('employeeID')
        ]);
    }
}
