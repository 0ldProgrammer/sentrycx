<?php 

namespace App\Modules\HistoricalRecords\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Modules\HistoricalRecords\Services\AuditRecordsService;

class AuditLogsFilterController extends Controller {

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

        $filters = $this -> service -> getAuditLogsFilters();

        return $filters;
    }
}