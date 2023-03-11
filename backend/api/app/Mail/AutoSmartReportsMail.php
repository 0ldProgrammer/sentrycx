<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Container\Container;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;


class AutoSmartReportsMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data_per_tab;
    public $report_type;
    public $user_data;
    public $list_of_thresholds_per_account;
    public $link;
    public $list_of_accounts;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data_per_tab, $report_type, $user_data, $list_of_thresholds_per_account, $link, $list_of_accounts)
    {
        $this->data_per_tab = $data_per_tab;
        $this->report_type = $report_type;
        $this->user_data = $user_data;
        $this->list_of_thresholds_per_account = $list_of_thresholds_per_account;
        $this->link = $link;
        $this->list_of_accounts = $list_of_accounts;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        
        $download_speed_threshold = array();
        $upload_speed_threshold = array();
        $ping_threshold = array();
        $cpu_threshold = array();
        $disk_threshold = array();
        $ram_threshold = array();

        $current_date = Carbon::now()->format('Ymd');

        $current_date_with_format = Carbon::now()->format('d/m/Y');

        $sharepoint_link = "https://cnxmail.sharepoint.com/:p:/r/Concentrix%20Central/Development/AppDev/_layouts/15/Doc.aspx?sourcedoc=%7B15A778A3-F61F-4D34-88C9-5572E0B68153%7D&file=Report%20Guide.pptx&action=edit&mobileredirect=true&cid=db0924cd-44a4-4ac7-8685-dad395b0958d";

        foreach ($this->list_of_thresholds_per_account as $value) {
   
            switch ($value->name) {
                case 'Download Speed':
                    array_push($download_speed_threshold, $value);
                break;

                case 'Upload Speed':  
                    array_push($upload_speed_threshold, $value);
                break;

                case 'Ping':
                    array_push($ping_threshold, $value);
                break;

                case 'CPU':
                    array_push($cpu_threshold, $value);
                break;

                case 'Disk':
                    array_push($disk_threshold, $value);
                break;

                case 'RAM':
                    array_push($ram_threshold, $value);
                break;
       
                default:
                   
              }
        }
       
        $this->exportReportType($this->data_per_tab, $this->report_type, $current_date);
        
        // fetch firstname
        $arr = explode(' ',trim($this->user_data->firstname));

        return $this->subject("SentryCX™ Auto Daily Reporting")
            ->from('noreply_sentrycx@concentrix.com', 'SentryCX™')
            ->view('emails.AutoSmartReportsMail', 
                [
                    'firstname' =>  $arr[0],
                    'current_date' => $current_date_with_format,
                    'download_speed_threshold' => $download_speed_threshold,
                    'upload_speed_threshold' => $upload_speed_threshold,
                    'ping_threshold' => $ping_threshold,
                    'cpu_threshold' => $cpu_threshold,
                    'disk_threshold' => $disk_threshold,
                    'ram_threshold' => $ram_threshold,
                    'link' => $this->link,
                    'sharepoint_link' => $sharepoint_link,
                    'list_of_accounts' => $this->list_of_accounts
                ])->attach(public_path("sentrycx_daily_reports_$current_date.xlsx"));

    }

    public function exportReportType($data_per_tab, $report_type, $current_date) {
       
        $worksheet_title = [
            '1' => 'Speedtest Threshold',
            '2' => 'Application Threshold',
            '3' => 'Offline Agents',
            '4' => 'Utilization',
            '5' => 'Restricted Applications',
            '6' => 'Required Applications'
        ]; 
       
        $spreadsheet = new Spreadsheet();

        $rowHeader = 1;
        $colLetters = 'A';
        $activeSheet = 0;
        
        $lastKey = key(array_slice($data_per_tab, -1, 1, true));
        
        foreach($data_per_tab as $index => $index_value) {
            $colHeader = 1;
            
            if($index != $lastKey) {
                $spreadsheet->createSheet();
            }
 
            $set_title = $worksheet_title[$report_type[$index]];
            
            $spreadsheet->setActiveSheetIndex($activeSheet);
            $spreadsheet->getActiveSheet()->setTitle($set_title);

            if (count($index_value) > 0) {

                $array_keys = array_keys($index_value[0]);
            
                foreach($array_keys as $header) {
                    $spreadsheet->getActiveSheet()->getStyleByColumnAndRow($colHeader, $rowHeader)->getFont()->setBold(true);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colHeader, $rowHeader, $header);
                    $spreadsheet->getActiveSheet()->getColumnDimension($colLetters)->setAutoSize(true);
                    $colHeader++;
                    $colLetters++;
                    
                }

                $row = 2;
                foreach($index_value as $field) {
                    $col = 1;
                    foreach($field as $value) {
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value);
                        $col++;
                    }
                    $row++;
                }
            }

            $activeSheet++;
        }
   
        // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');
        
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save(public_path("sentrycx_daily_reports_$current_date.xlsx"));
 
    }
}