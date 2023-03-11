<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;
use App\Console\Commands\ZohoSessionReports;
use App\Console\Commands\OfflineQueue;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\ZohoSessionReports::class,
        \App\Console\Commands\InverseSeeder::class,
        \App\Console\Commands\OfflineQueue::class,
        \App\Console\Commands\AppUpdaterCommand::class,
        \App\Console\Commands\StatsLoggerCommand::class,
        \App\Console\Commands\AgentConnectionLoggerCommand::class,
        \App\Console\Commands\DisableAgentCommand::class,
        \App\Console\Commands\AutomatedWipeoutCommand::class,
        \App\Console\Commands\AddUsersBasedFromMSACommand::class,
        \App\Console\Commands\RemoveUsersBasedFromMSACommand::class,
        \App\Console\Commands\AgentConnectionProfileSyncCommand::class,
        \App\Console\Commands\UpdateCnxEmployeesCommand::class,
        \App\Console\Commands\InsertCnxEmployeesCommand::class,
        \App\Console\Commands\DeviceStatusOnlineCommand::class,
        \App\Console\Commands\HourlyEmailSend::class,
        \App\Console\Commands\UpdateDesktopAppConfigCommand::class,
        \App\Console\Commands\AgentApplicationsMailCommand::class,
        \App\Console\Commands\UpdateVPNStatusCommand::class,
        \App\Console\Commands\SmartDailyReportingCommand::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule){
        $schedule -> command('zoho:report') -> hourly();
        $schedule -> command('offline-queue:work')-> everyThirtyMinutes();
        $schedule -> command('stats:logger') -> hourly();
        // $schedule -> command('agent:logger') -> everyThirtyMinutes();
        $schedule -> command('agent:scheduled-wipeout') -> daily();
        $schedule -> command('create:users') -> daily();
        $schedule -> command('remove:users') -> daily();
        $schedule -> command('update:cnx-employees') -> everyTwoHours() -> withoutOverlapping();
        $schedule -> command('insert:cnx-employees') -> daily();
	//$schedule -> command('agent:status-check') -> cron('0 4 * * *') -> withoutOverlapping();
	//$schedule -> command('agent:status-check') -> everyFiveMinutes() -> withoutOverlapping();
        $schedule -> command('email:hourlySend') -> hourly();
        $schedule -> command('update:app-config') -> everyFourHours();
        // $schedule -> command('mail:send-applications') -> daily();
        $schedule -> command('update:vpn-status') -> everySixHours();
        $schedule -> command('send:daily-reports') -> daily();
        // $schedule -> command('mail:send-applications') -> daily();
        // $schedule -> command('agent:status-check') -> cron('0 2 * * *');
        // $schedule -> command('agent:status-check') -> cron('0 8 * * *');
        // $schedule -> command('agent:status-check') -> cron('0 17 * * *');
    }
}
