<?php

namespace App\Modules\WorkstationModule\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Container\Container;
use Illuminate\Support\Arr;
use App\Modules\WorkstationModule\Services\AgentConnectionService;

class TOCConnectionsListController extends Controller {
    /** @var AgentConnectionService $service  */
    protected $service;

    /**
     * Constructor Dependencies
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct(Container $container){
        $this -> service = $container -> get('AgentConnectionService');
    }

    /**
     * Handles the list of agent connections
     *
     * @param Request $request
     * @return type
     * @throws conditon
     **/
    public function __invoke(Request $request ){
        $page = $request -> query('page', 1);

        $per_page = $request -> query('perPage');

        $conditions = Arr::only($request -> query(), ['location','account', 'agent_name', 'country', 'aux_status'] );

        return $this -> service -> getConnectedToc($page, $conditions, $per_page);
    }
}
