<?php 

namespace App\Modules\WorkstationModule\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Container\Container;
use App\Modules\WorkstationModule\Services\HostfileService;

class AccountsHostfileController {

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
    public function __invoke(Request $request, $account = '' ){
        $saved     = $request -> query('saved', FALSE);
        $extension =  ($saved) ? ".swp" : "";
        $content = $this -> service -> get( $account . $extension );

        return (new Response($content, 200 ))
            ->header('Content-Type', "text/plain");
    }
}
