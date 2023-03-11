<?php 

namespace App\Modules\Reports\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use App\Modules\Reports\Models\ReportTypePerUser;
use Illuminate\Container\Container;
use App\Modules\Reports\Services\ReportService;

class UpdateEmailNotificationsController extends Controller {

    /** @var ReportService $reportService */
    protected $reportService;

    /**
     *
     * Constructor method
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct(Container $container){
        $this -> reportService = $container -> get('ReportService');
    }

    /**
     *
     * 
     *
     * @param Request $request
     * @return type
     * @throws conditon
     **/
    public function __invoke(Request $request){

        return $this -> reportService -> updateEmailNotifications($request);   
    }
}