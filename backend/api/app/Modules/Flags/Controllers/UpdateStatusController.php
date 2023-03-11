<?php

namespace App\Modules\Flags\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Container\Container;

/**
 * UpdateFlagController
 */
class UpdateStatusController extends Controller
{
    /** @var \App\Modules\Flags\Services\FlagService $flagService FlagService */
    private $flagService = null;
    /**
     * Constructor dependencies
     *
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct(Container $container)
    {
        $this -> flagService = $container -> get('FlagService');
    }

    /**
     *
     * Handles the route function
     *
     * @param Request $request
     * @param int $id
     * @return type
     **/
    public function __invoke(Request $request, $id = 0)
    {
        $status = $request -> get('status');

        $data = $this -> flagService -> updateStatus( $id, $status );

        return ['status' => 'OK'];
    }
}
