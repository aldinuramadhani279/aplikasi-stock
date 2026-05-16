<?php

namespace App\Console;

use App\Jobs\SendDailyStockReport;
use App\Models\TelegramSetting;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Cek stok menipis setiap jam
        $schedule->command('stock:check-low')->hourly();

        // Laporan harian — waktu sesuai setting di database
        $reportTime = TelegramSetting::getValue('daily_report_time', '08:00');
        $dailyEnabled = TelegramSetting::getValue('daily_report_enabled', '1');

        if ($dailyEnabled) {
            $schedule->command('telegram:send-report')->dailyAt($reportTime);
        }
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
