<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    const FIRST_RUN_HOUR = 2;
    const SECOND_RUN_HOUR = 4;

    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\CronJob::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('CronJob:cronjob')
        ->everyFiveMinutes();
        $schedule->command('CronJob:scheduledrulejob')->cron('0 */2 * * *'); // Run every two hours

        $schedule->command('command:SendReminders')->mondays();
        $schedule->command('CronJob :sendemailforrejectedrequestsjob')->mondays()
            ->twiceDaily(1, 4);



    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
   }
