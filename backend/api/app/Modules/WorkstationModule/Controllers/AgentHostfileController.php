<?php 

namespace App\Modules\WorkstationModule\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Container\Container;
use App\Modules\WorkstationModule\Services\HostfileService;

class AgentHostfileController extends Controller {

    /** @var HostfileService $var description */
    protected $service;

    /**
     *
     * Undocumented function long description
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct(Container $container ){
        $this -> service = $container -> get('HostfileService');
    }

    /**
     *
     * Handles saving of hostfile
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __invoke(Request $request, $worker_id ){
        $content = $this -> service -> getWorkstation( $worker_id );

        return (new Response($content, 200 ))
            ->header('Content-Type', "text/plain");
    }
}
