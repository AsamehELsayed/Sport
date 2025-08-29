<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Schedule as ScheduleFacade;

class Scheduler
{
    public static function schedule(Schedule $schedule): void
    {
        // Process email campaigns every minute
        $schedule->command('email:process-campaigns')
            ->everyMinute()
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/email-campaigns.log'));

        // Clean up old campaign data (optional)
        $schedule->command('email:cleanup-old-campaigns')
            ->daily()
            ->at('02:00')
            ->appendOutputTo(storage_path('logs/email-cleanup.log'));
    }
}
