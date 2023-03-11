<?php

namespace App\Modules\HistoricalRecords\Services;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Carbon\Carbon;

class PingTraceRecordsService {

    /**
     * Retrieve the list of Timestamps in Ping
     *
     * @return type
     * @throws conditon
     **/
    public function fetchPingTimestamps($worker_id, $application)
    {
        $query = null;

        // old query
        // if ($application) {
        //     $query = DB::table('monitoring')
        //         -> select('created_at')
        //         -> where('application', $application)
        //         -> where('worker_id', $worker_id)
        //         -> orderBy('created_at', 'DESC') -> get();
        // }
        // return $query;

        if ($application) {
            $query = DB::table('monitoring')
                -> select('id', 'created_at')
                -> where('application', $application)
                -> where('worker_id', $worker_id)
                -> orderBy('created_at', 'DESC')
                -> limit(100) // previously unlimited
                -> get();
        }
        return $query;
    }

    /**
     * Retrieve the list of Applications in Ping
     *
     * @return type
     * @throws conditon
     **/
    public function fetchPingApplications($worker_id)
    {
        $query = null;
        $account = $this->getAccountByWorkerId($worker_id);
        // old query
        // $query = DB::table('monitoring')
        //     -> select('application')
        //     -> distinct()
        //     -> where('worker_id', $worker_id)
        //     -> orderBy('application', 'ASC')
        //     -> get();
        // return $query;

        $query = DB::table('applications')
            -> select('url')
            -> where('account', $account)
            -> where('is_loaded', 1)
            -> orderBy('url', 'ASC')
            -> get();
        return $query;
    }

    private function getAccountByWorkerId($worker_id)
    {
        $query = DB::table('agent_connections')
            -> select('account')
            -> where('worker_id', $worker_id)
            -> orderBy('id', 'DESC')
            -> limit(1);
        $data = $query->get();
        if(count($data) > 0) return $data[0]->account;
    }

    public function generateExcelReportFromPing($worker_id, $start_date, $end_date, $timezone){

        $new_data_array= array();

        $query = DB::table('monitoring')
            ->select('created_at', 'application', 'ping_ref', 'ping')
            -> whereRaw("created_at BETWEEN '$start_date' AND '$end_date'")
            -> where('worker_id', $worker_id)
            -> get();

        $data_array = json_decode(json_encode($query), true);

        foreach($data_array as $value){
            if(isset($value['created_at'])){
                $get_current_date = Carbon::createFromTimestamp(strtotime($value['created_at']))
                    ->timezone($timezone)
                    ->toDateTimeString();
    
                $value['created_at'] = Carbon::parse($get_current_date)->format('Y-m-d h:i:s a');
            } 
            array_push($new_data_array, $value);
        }

        $list_of_headers = [
            'Date', 'Application', 'Ping from Google', 'Ping from Application'
        ];
        
        $this -> excelStructure($new_data_array, $list_of_headers);

    }

    public function generateExcelReportFromTrace($worker_id, $start_date, $end_date, $timezone){

        $new_data_array= array();

        $query = DB::table('monitoring')
            ->select('created_at', 'application', 'traceroute_ref', 'traceroute')
            -> whereRaw("created_at BETWEEN '$start_date' AND '$end_date'")
            -> where('worker_id', $worker_id)
            -> get();

        $data_array = json_decode(json_encode($query), true);

        foreach($data_array as $value){
            if(isset($value['created_at'])){
                $get_current_date = Carbon::createFromTimestamp(strtotime($value['created_at']))
                    ->timezone($timezone)
                    ->toDateTimeString();
    
                $value['created_at'] = Carbon::parse($get_current_date)->format('Y-m-d h:i:s a');
            } 
            array_push($new_data_array, $value);
        }

        $list_of_headers = [
            'Date', 'Application', 'Traceroute from Google', 'Traceroute from Application'
        ];
        
        $this -> excelStructure($new_data_array, $list_of_headers);

    }

    private function excelStructure( $data_array, $list_of_headers) {

            $spreadsheet = new Spreadsheet();
        
            $sheet = $spreadsheet->getActiveSheet();

            $rowHeader = 6;
            $colHeader = 1;
            $colLetters = 'A';
            foreach($list_of_headers as $value) {
                $sheet->getStyleByColumnAndRow($colHeader, $rowHeader)->getFont()->setBold(true);
                $sheet->setCellValueByColumnAndRow($colHeader, $rowHeader, $value);
                $sheet->getColumnDimension($colLetters)->setAutoSize(true);
                $colHeader++;
                $colLetters++;
            }

            $row = 7;
            foreach($data_array as $value) {
                $col = 1;
                foreach($value as $specific_value) {
                    $sheet->setCellValueByColumnAndRow($col, $row, $specific_value);
                    $col++;
                }
                $row++;
            }

            $sheet->mergeCells("A1:A5");

            $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            $drawing->setName('SentryCX');
            $drawing->setDescription('SentryCX');
            $drawing->setPath('angular2-logo-white.png'); 
            $drawing->setCoordinates('A1');
            $drawing->setWidthAndHeight(100, 100);
            $drawing->setWorksheet($sheet);

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Cache-Control: max-age=0');
            header('Cache-Control: max-age=1');
            header('Cache-Control: cache, must-revalidate');
            header('Pragma: public');
        
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer -> save('php://output');
    }

}
