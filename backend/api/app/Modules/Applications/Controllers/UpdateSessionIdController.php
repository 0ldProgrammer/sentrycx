<?php

namespace App\Modules\Applications\Controllers;

use Illuminate\Container\Container;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Applications\Services\ApplicationService;

class UpdateSessionIdController extends Controller {

  
    /** @var ApplicationService $service */
    protected $service;

    /*
     *
     * Constructor dependency method
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct(Container $container ){
        $this -> service = $container -> get('ApplicationService');
    }
    /**
     *
     * 
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/

    public function __invoke(Request $request){
        $data = $request->input();

        $this -> service -> updateSessionId($data);

        return ["status" => "ok"];
    }

}
