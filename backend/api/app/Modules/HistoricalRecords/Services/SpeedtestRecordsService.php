<?php 

namespace App\Modules\HistoricalRecords\Services;

use App\Modules\HistoricalRecords\Models\LogsSpeedtest;
use App\Modules\HistoricalRecords\Services\HistoricalRecordsService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SpeedtestRecordsService extends HistoricalRecordsService {

    /** @var LogsSpeedtest $model  */
    protected $model;

    /** @var QueryBuilder $query description */
    protected $query;

    /** @var Array $fields description */
    protected $fields = ['worker_id', 'download_speed', 'upload_speed', 'connection_type', 'speedtest_source'];

    /**
     *
     * Constructor Dependencies
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct() {
        $this -> model = new LogsSpeedtest();
        $this -> query = LogsSpeedtest::query();
    }

    /**
     *
     * 
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function recent( $count = 2 ){
        $recent_results = [];

        $collections =  $this -> query -> where('worker_id', $this -> workerID)
            -> orderBy('created_at', 'DESC')
            -> limit($count)
            -> get();


        foreach( $collections as $collection ) {
            $recent_results[] = [
                'upload_speed' => "{$collection->connection_type}:{$collection->upload_speed} Mbps  ( {$collection->created_at} )",
                'download_speed' => "{$collection->connection_type}:{$collection->download_speed} Mbps ( {$collection->created_at} )"
            ];
        }

        return $recent_results;
    }

    
    public function generateExcelReportFromSpeedtest($worker_id, $start_date, $end_date, $timezone) {

        $new_data_array = array();

        $data = DB::table('logs_speedtest')
            -> select('created_at', 'connection_type', 'download_speed', 'upload_speed')
            -> whereRaw("created_at BETWEEN '$start_date' AND '$end_date'")
            -> where('worker_id', $worker_id)
            -> get();

        $data_array = json_decode(json_encode($data), true);

        foreach($data_array as $value){
            if(isset($value['created_at'])){
                $get_current_date = Carbon::createFromTimestamp(strtotime($value['created_at']))
                    ->timezone($timezone)
                    ->toDateTimeString();
    
                $value['created_at'] = Carbon::parse($get_current_date)->format('Y-m-d h:i:s a');
            }
            
            $value['download_speed'] = $value['download_speed'] && $value['download_speed'] !== ' - ' ? $value['download_speed'].' Mbps' : '-';
            $value['upload_speed'] = $value['upload_speed'] && $value['upload_speed'] !== ' - ' ? $value['upload_speed'].' Mbps' : '-'; 

            array_push($new_data_array, $value);
        }

        $list_of_headers = [
            'Timestamp', 'Connection Type', 'Download Speed', 'Upload Speed'
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
