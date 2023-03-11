<?php 

namespace App\Modules\Workday\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Container\Container;
use App\Modules\Workday\Services\EmployeeService;

class ProfileController extends Controller {

    /** @var EmployeeService $service  */
    protected $service = null;


    const STATUS_REF = [
        0 => 'NOT_FOUND',
        1 => 'OK'
    ];

    /**
     *
     * Constructor Dependencies
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct(Container $container){
        $this -> service  = $container -> get('EmployeeService');
    }

    /**
     *
     * Handles the fetching of employee profile
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __invoke(Request $request, $worker_id  = 0){
        $profile = $this -> service -> get( $worker_id );
        $index   = count($profile );

        return [
            'status' =>  self::STATUS_REF[ $index ] ,
            'profile' => ($index) ? $profile[0] : []
        ];
    }
}