<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $keep = max((int) config('backup.keep', 14), 1);
        $time = config('backup.schedule_time', '01:00');
        $command = "app:backup-data --keep={$keep}";

        if ((bool) config('backup.include_env', false)) {
            $command .= ' --include-env';
        }

        $schedule->command($command)->dailyAt($time)->withoutOverlapping();
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
