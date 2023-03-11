<?php 

namespace App\Modules\HistoricalRecords\Services;

use App\Modules\HistoricalRecords\Models\LogsMOS;
use Carbon\Carbon;
use App\Interfaces\LoggerModel;
use Illuminate\Database\Eloquent\Builder;

class HistoricalRecordsService {

    /** @var LoggerModel $model description */
    protected $model;

    /**
     *
     * Getter for the ModelInstance
     *
     * @param LoggerModel $var Description
     * @return type
     * @throws conditon
     **/
    public function getModel(){
        return $this -> model;
    }

    /** @var Array $fields description */
    protected $fields = [];

    /**
     *
     * Getter for fields
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function getFields(){
        return $this -> fields;
    }

    /** @var Builder $query description */
    protected $query;

    /** @var String $workerID description */
    protected $workerID ;


    
    /**
     *
     * Setter for workerID
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function setWorkerID($workerID){
        $this -> workerID = $workerID;
        return $this;
    }

    /**
     *
     * Log the data
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function log( $data ){
        foreach( $this -> fields as $field ) {
            $value = "-";

            if( array_key_exists($field, $data ) )
                $value = $data[$field];

            

            $this -> model -> $field = $value;
        }
            
        
        $this -> model -> worker_id = $this -> workerID;
    
        $this -> model -> save();

        return $this -> model;
    }

    /**
     *
     * Retrieve the historical data in the last 3 days
     *
     * @return type
     * @throws conditon
     **/
    public function get(){
        
        $last_three_days = Carbon::now()->subDays(3);

        return $this -> query -> where('worker_id', $this -> workerID)
            -> where('created_at', '>', $last_three_days )
            -> orderBy('created_at','DESC')
            -> get();
    }

    /**
     *
     * Retrieve the historical data in paginate format
     *
     * @return type
     * @throws conditon
     **/
    public function query( $page, $perPage = 1 ){
        $yesterday = Carbon::yesterday();

        die($yesterday);

        return $this -> query -> where('worker_id', $this -> workerID)
            -> where('created_at', '>', $yesterday )
            -> paginate($perPage, ['*', 'created_at as date_created'], 'page', $page );
    }


    /**
     *
     * Retrieve the historical data in paginate format
     *
     * @return type
     * @throws conditon
     **/
    public function queryRecent( $recent = 20 ){
        return $this -> query -> where('worker_id', $this -> workerID)
            -> orderBy('id', 'desc')
            -> limit( $recent )
            -> get();
    }

    public function getHistoricalLogs($workerID) {
        return LogsMOS::query()
            -> where('worker_id', $workerID)
            -> limit(20)
            -> get();
    }
}