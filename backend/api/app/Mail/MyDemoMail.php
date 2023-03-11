<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border;

class MyDemoMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {

        
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $spreadsheet = new Spreadsheet();
        $active_sheet = $spreadsheet->getActiveSheet();
        $active_sheet->setTitle('Tableau général');

        $active_sheet->setCellValue('A1', 'ajshajk');
        $active_sheet->setCellValue('B1', 'fdg');
        $active_sheet->setCellValue('C1', 'sure na');


        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('test.xlsx');

        return $this->view('emails.myDemoMail')
                    ->attach(public_path('test.xlsx'));
    }
}