<?php

namespace App\Modules\WorkstationModule\Services;

use App\Modules\WorkstationModule\Models\WorkstationProfile;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class HostfileService {


    /** @var FilesystemManager $storage*/
    protected $storage = null;

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
     * Retrieve the hostfile saved, if none saved, return a placeholder/default string
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function get($account){
        try {
            $path = "hostfiles/{$account}";
            
            return $this -> storage -> get( $path );
        }
        catch( FileNotFoundException $e ){
            return file_get_contents( resource_path() . "/views/stubs/hostfile.stub");
        }
        
    }

    /**
     *
     * Update centralized hostfile
     *
     * @param String $account
     * @param String $content
     * @return type
     * @throws conditon
     **/
    public function save($account, $content){
        $path = "hostfiles/$account";
        
        return $this -> storage -> put( $path, $content );
    }

    /**
     * Update the hostfile of workstation profile
     *
     *
     * @param String $worker_id Description
     * @return type
     * @throws conditon
     **/
    public function updateWorkstation($worker_id, $content ){
        WorkstationProfile::where('worker_id', $worker_id)
            ->update(['host_file' => $content]);
    }

    /**
     *
     * Get the workstation's hostfile
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function getWorkstation($worker_id){
        $profile = WorkstationProfile::where('worker_id', $worker_id)
            -> where('redflag_id', 0)
            -> firstOrFail();

        return $profile -> host_file;
    }
}