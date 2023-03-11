<?php 

namespace App\Modules\HistoricalRecords\Services;

use App\Modules\HistoricalRecords\Models\LogsWorkstationTrace;
use App\Modules\HistoricalRecords\Services\HistoricalRecordsService;

class WorkstationTraceRecordsService extends HistoricalRecordsService {

    /** @var LogsWorkstationTrace $model description */
    protected $model; 

    /** @var Type $query */
    protected $query;

    /** @var Array $var description */
    protected $fields = ['stacktrace', 'timelog', 'workstation'];


    /**
     *
     * Dependency Constructor Injection
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct(){
        $this -> model = new LogsWorkstationTrace();
        $this -> query = LogsWorkstationTrace::query();
    }
    
}