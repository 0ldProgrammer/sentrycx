<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\Reports\Services\ReportService;
use Illuminate\Container\Container;

class SmartDailyReportingCommand extends Command
{
    /** @var ReportService $service  */
    protected $service;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:daily-reports';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run Daily Reports';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Container $container){
        $this -> service = $container -> get('ReportService');
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(){
        
        $this -> service -> sendDailyReports();

        echo "Done sending the reports.";

    }
}
