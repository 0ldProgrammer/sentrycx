<?php 

namespace App\Modules\Auth\Services;

use Illuminate\Filesystem\FilesystemManager;

class InstallerService {


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
     * Generate the download URL 
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function downloadDesktop(  ){
        $path = "installers/setup.exe";
        $url = $this -> storage -> download( $path );
        return $url;
    }
}