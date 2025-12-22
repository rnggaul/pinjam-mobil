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
        $schedule->command('bookings:expire')->dailyAt('00:01');
        $schedule->command('bookings:notify-admin')->dailyAt('08:00');

        // Testing purposes
        // $schedule->command('bookings:expire')->everyMinute();
        // $schedule->command('bookings:notify-admin')->everyMinute();
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
