<?php

namespace App\Modules\Maintenance\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Container\Container;
use App\Http\Controllers\Controller;

class UserAddMSAController extends Controller {

    /** @var \App\Modules\Maintenance\Services\UserService $service  */
    protected $service;

    /**
     *
     * Constructor Dependencies
     *
     * @param Containr $container
     * @return type`
     * @throws conditon
     **/
    public function __construct(Container $container ){
        $this -> service = $container -> get('UserService');
    }

    /**
     *
     * Handles adding of MSA
     *
     * @param Request $request
     * @return type
     * @throws conditon
     **/
    public function __invoke(Request $request ){
        $tagging = $request -> input('tagging');
        $programme_msa = $request -> input('programme_msa');
  
        $this -> service -> saveMSAUsers( $tagging, $programme_msa );

        return ['status' => 'OK', 'message' => 'Programme MSA successfully created/updated!'];
    }
}
