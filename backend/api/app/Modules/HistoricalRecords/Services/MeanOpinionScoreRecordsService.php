<?php 

namespace App\Modules\HistoricalRecords\Services;

use App\Modules\HistoricalRecords\Models\LogsMOS;
use App\Modules\HistoricalRecords\Services\HistoricalRecordsService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Log;

class MeanOpinionScoreRecordsService extends HistoricalRecordsService {

    /** @var LogsMOS $model description */
    protected $model; 

    /** @var Type $query */
    protected $query;

    /** @var Array $var description */
    protected $fields = ['jitter','average_latency', 'mos', 'packet_loss'];

    /**
     *
     * Initialize necessary Model and Query 
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct(){
        $this -> model = new LogsMOS();
        $this -> query = LogsMOS::query();
    }

    /**
     *
     * OVERRIDE : Log the data to make it round to 2 decimal place
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function log( $data ){
        foreach( $this -> fields as $field ){ 
		$this -> model -> $field = round((float)$data[$field], 2);
	}
        
        $this -> model -> worker_id = $this -> workerID;
    
        $this -> model -> save();

        return $this -> model;
    }

    /**
     *
     * OVERIDE : Retrieve the 20 recent historical data 
     *
     * @return type
     * @throws conditon
     **/
    public function get(){
        return $this -> query -> where('worker_id', $this -> workerID)
            -> orderBy('id', 'desc')
            -> limit( 20 )
            -> get();
    }

    public function generateExcelReportFromMOS($worker_id, $start_date, $end_date, $timezone) {
  
        $new_data_array= array();
        
        $data = DB::table('logs_mos')
            -> select('created_at', 'average_latency', 'packet_loss', 'jitter', 'mos')
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
            array_push($new_data_array, $value);
        }

        $list_of_headers = [
            'Timestamp', 'AVG Latency', 'Packet Loss', 'Jitter', 'MOS'
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