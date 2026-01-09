<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\CleanupTemporaryFiles;
use App\Jobs\CleanupOrphanedFiles;

/**
 * Console Kernel
 * 
 * Prompt 417: Schedule Cleanup Jobs
 * 
 * Registers console commands and schedules recurring jobs.
 * Cleanup jobs run automatically based on configured schedule.
 */
class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->job(new CleanupTemporaryFiles())
            ->daily()
            ->at('02:00')
            ->name('cleanup-temporary-files')
            ->description('Clean up temporary files older than retention period')
            ->withoutOverlapping()
            ->onOneServer()
            ->runInBackground();

        $schedule->job(new CleanupOrphanedFiles())
            ->weekly()
            ->sundays()
            ->at('03:00')
            ->name('cleanup-orphaned-files')
            ->description('Clean up orphaned files not referenced in database')
            ->withoutOverlapping()
            ->onOneServer()
            ->runInBackground();

        $schedule->command('queue:prune-batches --hours=48')
            ->daily()
            ->at('04:00')
            ->name('prune-job-batches')
            ->description('Prune old job batches');

        $schedule->command('cache:prune-stale-tags')
            ->hourly()
            ->name('prune-stale-cache-tags')
            ->description('Prune stale cache tags');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
