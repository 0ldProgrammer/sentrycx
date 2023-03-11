<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Modules\WorkstationModule\Services\AgentConnectionService;
use Illuminate\Container\Container;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

class MailOnlineAgents extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($service)
    {
        $this -> service = $service;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        $conditions['is_admin'] = false;
        $columns = 'ac.worker_id, agent_name, agent_email, ac.job_profile,  wd.msa_client, location_name, connection_type, net_type, isp_fullname,  download_speed, upload_speed, mos, ac.lob, desktop_app_version';
        $headers = ['Employee ID', 'Agent Name', 'Agent Email', 'Position', 'Account', 'Location', 'Connection', 'Agent','ISP','Download Speed','Upload Speed', 'MOS', 'LOB', 'Desktop Version'];

        $details = $this -> service -> getRawDataForExcel($conditions, $columns);

        $this -> generateExcel($details['data_array'], $headers);

        return $this->subject("SentryCX™ Online Agents as of ".date('F d, Y - g:i A UTC'))
                    ->view('emails.MailOnlineAgents')
                    ->attach(public_path('onlineAgents.xlsx'));
    }

    public function generateExcel($data_array, $list_of_headers)
    {
        $spreadsheet = new Spreadsheet();
        
        $sheet = $spreadsheet->getActiveSheet();

        $rowHeader = 2;
        $colHeader = 1;
        $colLetters = 'A';
        foreach($list_of_headers as $value) {
            $sheet->getStyleByColumnAndRow($colHeader, $rowHeader)->getFont()->setBold(true);
            $sheet->setCellValueByColumnAndRow($colHeader, $rowHeader, $value);
            $sheet->getColumnDimension($colLetters)->setAutoSize(true);
            $colHeader++;
            $colLetters++;
        }

        $row = 3;
        foreach($data_array as $value) {
            $col = 1;
            foreach($value as $specific_value) {
                $sheet->setCellValueByColumnAndRow($col, $row, $specific_value);
                $col++;
            }
            $row++;
        }

        // $sheet->mergeCells("A1:A5");

        // $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        // $drawing->setName('SentryCX');
        // $drawing->setDescription('SentryCX');
        // $drawing->setPath(public_path('angular2-logo-white.png')); 
        // $drawing->setCoordinates('A1');
        // $drawing->setWidthAndHeight(100, 100);
        // $drawing->setWorksheet($sheet);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save(public_path('onlineAgents.xlsx'));

        return;

    }
}