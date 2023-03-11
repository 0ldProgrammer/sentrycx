<?php 

namespace App\Modules\Reports\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Container\Container;
use App\Modules\Reports\Services\ConnectionReportService;
use App\Modules\Reports\Services\ReportService;

class ReportDetailsController extends Controller {


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
     * Handles the excel generation of 30 mins interval summary
     *
     * @param Request $request
     * @return type
     * @throws conditon
     **/
    public function __invoke(Request $request){
       
        $report_type = $request -> input('reportTypes');
        $report_details = array();
        $reportHeader = array();
        foreach($report_type as $report)
        {
            $query = json_decode($this -> processReport(strtolower(preg_replace('/\s+/', '_', $report)), (object) $request -> input()));
            
            if($query->count > 0)
            {
                $query = $query->data;
                $queryHeader = json_decode(json_encode($query[0]),true);
                $queryHeader = array_keys($queryHeader);

                $report_details[$report] = $query;
                $reportHeader[$report] = $queryHeader;
            }
        }

        // return $query;

        //send to export function
        // print_r($report_details);
        if(!empty($report_details))
        {
            return $this -> reportService -> exportReport($report_details, $reportHeader);
            
        }else{
            return response()->json(array('status' => false));
        }
    }

    function processReport($report_type, $details)
    {
        $query = '';
        switch($report_type)
        {
            case 1: // MOS
                $query = $this -> getQuery('mos', $details);
            break;
            case 2: // SPEEDTEST
                $query = $this -> reportService -> querySpeedtest($details);
            break;  
            case 3:// MTR
                $query = $this -> reportService -> queryMTR($details);
            break;  
            case 4: // PING
                $query = $this -> reportService -> queryPING($details);
            break;    
            case 5://  RESTRICTED & REQUIRED APPS
                $query = $this -> reportService -> queryResReqApps($details);
            break;    
            case 6: // OFFLINE WORKSTATIONS
                $query = $this -> reportService -> queryOfflineWorkstation($details);
            break;    
            case 7: // UTILIZATIONS CPU
                $query = $this -> reportService -> queryUtilizationsCPU($details);
            break;    
            case 8: // UTILIZATIONS RAM
                $query = $this -> reportService -> queryUtilizationsRAM($details);
            break;    
            case 9: // UTILIZATIONS RAM USAGE
                $query = $this -> reportService -> queryUtilizationsRAMUsage($details);
            break;    
            case 10: // UTILIZATIONS DISK
                $query = $this -> reportService -> queryUtilizationsDISK($details);
            break;    
            case 11: // UTILIZATIONS DISK
                $query = $this -> reportService -> queryUtilizationsFreeDisk($details);
            break;    
        
        }


        return $query;
    }

    function getQuery($table, $params = null)
    {
        return $this -> reportService -> getReportQuery($table, $params);
    }

    function getLocationByAccount($account)
    {
        return $this ->  reportService -> getLocationByAccount($account);
    }

    function getThreshold($report_id, $account=null)
    {
        return $this ->  reportService -> getThreshold($report_id,$account);
    }

    function getReportType()
    {
        return $this -> reportService -> getReportType();
    }

}