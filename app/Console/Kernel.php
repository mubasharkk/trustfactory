<?php

namespace App\Console;

use App\Jobs\GenerateDailySalesReportJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    public function schedule(Schedule $schedule): void
    {
        // Run daily sales report at configured time
        $schedule->job(new GenerateDailySalesReportJob)->dailyAt(
            config('trustfactory.daily_sales_report_time', '00:00')
        );
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
