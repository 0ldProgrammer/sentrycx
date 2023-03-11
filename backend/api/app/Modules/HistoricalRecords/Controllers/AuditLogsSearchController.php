<?php 

namespace App\Modules\HistoricalRecords\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Modules\HistoricalRecords\Services\AuditRecordsService;

class AuditLogsSearchController extends Controller {

    /** @var AuditRecordsService $service */
    protected $service;

    /**
     *
     * Constructor Method
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct(Container $container){
        $this -> service = $container -> get('AuditRecordsService');
    }

    /**
     *
     * Handles the fetching of audit logs
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/

    public function __invoke(Request $request ){

        $search = $request -> query('search');
        
        $page = $request -> query('page', 1);
        
        $conditions = Arr::only($request -> query(), ['user', 'event', 'affected_agent', 'workstation_number' ] );

        $sort = $request -> only('sortBy', 'sortOrder');

        if( $sort )
            $this -> service -> setSort( $sort['sortBy'], $sort['sortOrder']);
        
        return $this -> service -> getAuditLogs($page, $conditions, $search);
    }
}