<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Container\Container;
use Illuminate\Support\Carbon;

class ZohoSessionReports extends Command
{
    /** @var  */
    protected $service;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zoho:report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Task Scheduler to extract zoho reports.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Container $container ){
        $this -> service = $container -> get('ZohoReportAPI');
        $this -> logService = $container -> get('ZohoLogService');
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(){
        echo "STARTING Zoho Session Reports Command ". PHP_EOL;

        $todate   = (Carbon::now()-> getPreciseTimestamp(3) );

        $fromdate = (Carbon::now()-> addHours(-1) -> getPreciseTimestamp(3) );

        $data = $this -> service -> extractReport($fromdate, $todate);

        foreach( $data -> representation as $session ){
            echo "CHECKING SESSION ID : {$session->session_id} ". PHP_EOL;
            $this -> logService -> updateReport($session -> session_id,  (array) $session );
        }

        echo "DONE Zoho Session Reports Command ". PHP_EOL;

        return;
    }
}
