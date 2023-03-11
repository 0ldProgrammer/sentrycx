<?php

namespace App\Modules\Flags\Controllers;

use Illuminate\Container\Container;
use App\Http\Controllers\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Modules\Flags\Services\FlagService;


class BatchUpdateStatusController extends BaseController{

    /** @var FlagService $vservice */
    protected $service;


    /**
     *
     * Constructor Dependencies
     *
     * @param Container $container
     * @return void
     * @throws conditon
     **/
    public function __construct( Container $container ){
        $this -> service = $container -> get('FlagService');
    }

    /**
     *
     * Handles the updating of status by batch
     *
     * @param Request $request
     * @return Response
     * @throws conditon
     **/
    public function __invoke( Request $request ){
        $args = $request -> only('conditions', 'status');

        $updated_count = $this -> service -> updateStatusMultiple( $args['status'], $args['conditions'] );

        return ['status' => 'OK', 'msg' => "$updated_count Issues has been updated to {$args['status']}" ];
    }

}

