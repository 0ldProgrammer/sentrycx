<?php 

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Auth\Services\InstallerService;
use Illuminate\Container\Container;

class DesktopAppDownloadController extends Controller {

    /** @var InstallerService $service description */
    protected $service;

    /** 
     *
     * Constructor dependencies
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct( Container $container ){
        $this -> service = $container -> get('InstallerService');
    }

    /**
     *
     * Handles the download of desktop installer
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __invoke(Request $request){
        $stream = $this -> service -> downloadDesktop();

        return $stream -> send();
    }
}