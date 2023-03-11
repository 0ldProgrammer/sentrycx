<?php 

namespace App\Modules\HistoricalRecords\Services;

use App\Modules\HistoricalRecords\Models\LogsAudit;
use App\Modules\HistoricalRecords\Services\HistoricalRecordsService;
use App\Modules\WorkstationModule\Helpers\ConnectedAgentsQueryHelper;
use Illuminate\Support\Facades\DB;
use DateTime;
use Carbon\Carbon;

class AuditRecordsService extends HistoricalRecordsService {

    /** @var LogsAudit $model  */
    protected $model;

    /** @var QueryBuilder $query description */
    protected $query;

    /** @var Array $fields description */
    protected $fields = ['user','event','affected_agent','workstation_number', 'worker_id'];

    /**
     *
     * Constructor Dependencies
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct() {
        $this -> model = new LogsAudit();
        $this -> query = LogsAudit::query();
    }

    /** @var String $sortBy */
    protected $sortBy = null;
    protected $sortOrder = 'desc';

    public function setSort($field, $order){
        $this -> sortBy = $field;
        $this -> sortOrder =  strtolower( $order );
    }

    public function getAuditLogs($page = 1 , $conditions = [], $search = "", $per_page = 20)
    {
       
        $query = LogsAudit::from('audit_logs as al');

        $field_mapping = [
            'user' => 'user',
            'startDate' => 'date_triggered',
            'endDate' => 'date_triggered',
            'affected_agent' => 'affected_agent',
            'event' => 'event',
            'workstation_number' => 'workstation_number',
            'worker_id' => 'worker_id'
        ];

   
        foreach( $conditions as $field_ref => $value  ){
            $field = $field_mapping[$field_ref];
            
            if ($field == 'user' 
                    || $field == 'affected_agent' 
                    || $field == 'workstation_number'
                    || $field == 'event'
                ) {
                $field_value = implode('', $value);
                $query -> where($field, 'like', "%{$field_value}%");
                continue;
            }
            
            if ($field_ref == 'startDate') {
                $field_value = implode('', $value);
                $query -> where($field, '>=', $field_value );
                continue;
            }
            if ($field_ref == 'endDate') {
                $field_value = implode('', $value);
                $add_day = Carbon::parse($field_value)->addDay();
                $query -> where($field, '<=', $add_day );
                continue;
            }

            $query -> where($field, $value );
        }
 

        if( $this -> sortBy ) {
            $query -> orderBy( $this -> sortBy, $this -> sortOrder );
        } else {
            $query -> orderBy('al.id', 'desc');
        }

        if($search != "") {
            $query ->where('user', 'like', "%$search%")
                    ->orWhere('event', 'like', "%$search%")
                    ->orWhere('affected_agent', 'like', "%$search%");
        }
        
        return $query -> paginate($per_page, ['*'], 'page', $page ); 
    }

        /**
     *
     * Retrieve the filters for search
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function getAuditLogsFilters()
    {
        return [
            'user'  => $this -> _getDistinctValues('user')
        ];
    }

    /**
     * Retrieve all the unique values for specific columns
     * which can then be used for filter referrence
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    private function _getDistinctValues($column_name = '', $table_name = 'audit_logs'){
        return DB::table($table_name)->distinct()
            -> get([$column_name])
            -> map( function( $item ) use($column_name) {
                return $item -> $column_name;
            });
    }

}
