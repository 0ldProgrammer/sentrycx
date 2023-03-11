<?php 

namespace App\Modules\HistoricalRecords\Controllers;

use App\Modules\HistoricalRecords\Services\SecureCXRecordsService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Container\Container;

class SecureCXUrlsController extends Controller {

    /** @var SecureCXRecordsService $service */
    protected $service;

    /*
     *
     * Constructor dependency method
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct(Container $container ){
        $this -> service = $container -> get('SecureCXRecordsService');
    }

    /**
     *
     * Handles the list of Accounts API
     *
     * @param Request $request
     * @return type
     * @throws conditon
     **/
    public function __invoke(Request $request){
        return $this -> service -> fetchUrls();
        return ['status' => 'OK', 'data' => $data];
    }
}