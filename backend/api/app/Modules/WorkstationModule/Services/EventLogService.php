<?php

namespace App\Modules\WorkstationModule\Services;

use Illuminate\Filesystem\FilesystemManager;

class EventLogService {


    /** @var \Illuminate\Filesystem\FilesystemManager $storage*/
    protected $storage = null;

    /** @var String $workerID description */
    protected $workerID;

    /** @var String $dateStart description */
    protected $dateStart;

    /** @var String $dateEnd description */
    protected $dateEnd;

    /** @var String $keyword description */
    protected $keyword;

    /** @var String $filename description */
    protected $filename;

    /** @var String $type description */
    protected $type;


    /** @var Boolean $type description */
    protected $tmp = false;

    /**
     *
     * Setter for properties
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function setWorkerID($value){
        $this -> workerID = $value;

        return $this;
    }

    /**
     *
     * Setter for properties
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function setDateStart($value){
        $this -> dateStart = $value;

        return $this;
    }

    /**
     *
     * Setter for properties
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function setDateEnd($value){
        $this -> dateEnd = $value;

        return $this;
    }

    /**
     *
     * Getter for properties
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function getFilename(){
        return $this -> filename;
    }

    /**
     *
     * Setter for properties
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function setFilename($value){
        $this -> filename = $value;

        return $this;
    }


    /**
     *
     * Setter for properties
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function setType($value){
        $this -> type = $value;

        return $this;
    }

    /**
     *
     * Setter for properties
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function setKeyword($value){
        $this -> keyword = $value;

        return $this;
    }

    /**
     *
     * Flag the log as temporary
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function init(){
        $this -> tmp = true;

        return $this;
    }

    /**
     *
     * Constructor dependencies
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct( FilesystemManager $storage ){
        $this -> storage = $storage;
    }


    /**
     *
     * Store the event log to file
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function logEvent($content = ''){
        if(!$this -> filename )
            $this -> generateFilename();

        if( $this -> tmp )
            $this -> filename = str_replace('.log', '.tmp', $this -> filename );
        else {
            $tmp_file = "$this->workerID/". str_replace('.log', '.tmp', $this -> filename );

            $this -> storage -> delete( $tmp_file );
            $this -> filename = str_replace('.tmp', '.log', $this -> filename );

        }


        $path = "{$this->workerID}/{$this->filename}";

        return $this -> storage -> put( $path , $content );
    }

    public function downloadPath( $path ){
        $url = $this -> storage -> download($path );
        return $url;
    }

    /**
     *
     * Retrieve all of the files based on workerID
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function getLogs( $workerID ){
        return $this -> storage -> files($workerID);
    }


    /**
     *
     * Generate file name format
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function generateFilename(){
        $filename = implode("_",[
            time(),
            $this -> type,
            $this -> dateStart,
            $this -> dateEnd,
            $this -> keyword
        ]);

        $this -> filename = $filename;

        return $filename;
    }
}
