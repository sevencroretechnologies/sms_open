<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\CleanupTemporaryFiles;
use App\Jobs\CleanupOrphanedFiles;
use App\Services\StorageStatisticsService;

/**
 * File Cleanup Command
 * 
 * Prompt 416: Create File Cleanup Command
 * 
 * Artisan command for manual file cleanup operations.
 * Supports cleaning temporary files, orphaned files, or both.
 * Can run in dry-run mode for testing.
 * 
 * Usage:
 * - php artisan files:cleanup --temp          Clean temporary files
 * - php artisan files:cleanup --orphan        Clean orphaned files
 * - php artisan files:cleanup --all           Clean both
 * - php artisan files:cleanup --dry-run       Preview without deleting
 * - php artisan files:cleanup --stats         Show storage statistics
 */
class CleanupFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'files:cleanup 
                            {--temp : Clean temporary files}
                            {--orphan : Clean orphaned files}
                            {--all : Clean both temporary and orphaned files}
                            {--dry-run : Preview what would be deleted without actually deleting}
                            {--stats : Show storage statistics}
                            {--days= : Override max age in days for temp files}
                            {--sync : Run synchronously instead of queuing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up temporary and orphaned files from storage';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        if ($this->option('stats')) {
            return $this->showStatistics();
        }

        $cleanTemp = $this->option('temp') || $this->option('all');
        $cleanOrphan = $this->option('orphan') || $this->option('all');
        $dryRun = $this->option('dry-run');
        $sync = $this->option('sync');

        if (!$cleanTemp && !$cleanOrphan) {
            $this->error('Please specify what to clean: --temp, --orphan, or --all');
            $this->line('');
            $this->line('Options:');
            $this->line('  --temp      Clean temporary files older than retention period');
            $this->line('  --orphan    Clean files not referenced in database');
            $this->line('  --all       Clean both temporary and orphaned files');
            $this->line('  --dry-run   Preview what would be deleted');
            $this->line('  --stats     Show storage statistics');
            $this->line('  --days=N    Override max age in days for temp files');
            $this->line('  --sync      Run synchronously instead of queuing');
            return Command::FAILURE;
        }

        if ($dryRun) {
            $this->warn('Running in DRY-RUN mode - no files will be deleted');
            $this->line('');
        }

        if ($cleanTemp) {
            $this->cleanTemporaryFiles($dryRun, $sync);
        }

        if ($cleanOrphan) {
            $this->cleanOrphanedFiles($dryRun, $sync);
        }

        return Command::SUCCESS;
    }

    /**
     * Clean temporary files.
     *
     * @param bool $dryRun
     * @param bool $sync
     * @return void
     */
    protected function cleanTemporaryFiles(bool $dryRun, bool $sync): void
    {
        $this->info('Cleaning temporary files...');

        $days = $this->option('days') ? (int) $this->option('days') : null;

        if ($sync) {
            $this->line('Running synchronously...');
            
            $job = new CleanupTemporaryFiles($days);
            $job->handle();
            
            $this->info('Temporary file cleanup completed.');
        } else {
            CleanupTemporaryFiles::dispatch($days);
            $this->info('Temporary file cleanup job dispatched to queue.');
        }
    }

    /**
     * Clean orphaned files.
     *
     * @param bool $dryRun
     * @param bool $sync
     * @return void
     */
    protected function cleanOrphanedFiles(bool $dryRun, bool $sync): void
    {
        $this->info('Cleaning orphaned files...');

        if ($sync) {
            $this->line('Running synchronously...');
            
            $job = new CleanupOrphanedFiles($dryRun);
            $job->handle();
            
            $this->info('Orphaned file cleanup completed.');
        } else {
            CleanupOrphanedFiles::dispatch($dryRun);
            $this->info('Orphaned file cleanup job dispatched to queue.');
        }
    }

    /**
     * Show storage statistics.
     *
     * @return int
     */
    protected function showStatistics(): int
    {
        $this->info('Gathering storage statistics...');
        $this->line('');

        $service = app(StorageStatisticsService::class);
        $stats = $service->getOverallStatistics();

        $this->line('<fg=cyan>Overall Storage Statistics</>');
        $this->line('');

        $this->table(
            ['Metric', 'Public Storage', 'Private Storage', 'Total'],
            [
                [
                    'Files',
                    $stats['public_storage']['files'],
                    $stats['private_storage']['files'],
                    $stats['total']['files'],
                ],
                [
                    'Size',
                    $stats['public_storage']['size_formatted'],
                    $stats['private_storage']['size_formatted'],
                    $stats['total']['size_formatted'],
                ],
            ]
        );

        $this->line('');
        $this->line('<fg=cyan>Storage by Module</>');
        $this->line('');

        $moduleStats = $service->getModuleStatistics();
        $moduleData = [];

        foreach ($moduleStats['modules'] as $module => $categories) {
            $moduleFiles = 0;
            $moduleSize = 0;
            
            foreach ($categories as $category => $catStats) {
                $moduleFiles += $catStats['files'];
                $moduleSize += $catStats['size_bytes'];
            }
            
            if ($moduleFiles > 0) {
                $moduleData[] = [
                    ucfirst($module),
                    $moduleFiles,
                    $this->formatBytes($moduleSize),
                ];
            }
        }

        if (!empty($moduleData)) {
            $this->table(['Module', 'Files', 'Size'], $moduleData);
        } else {
            $this->line('No files found in any module.');
        }

        $this->line('');
        $this->line('<fg=cyan>Database References</>');
        $this->line('');

        $dbStats = $service->getDatabaseStatistics();
        $dbData = [];

        foreach ($dbStats as $key => $value) {
            if ($key !== 'generated_at' && is_int($value)) {
                $dbData[] = [
                    str_replace('_', ' ', ucfirst($key)),
                    $value,
                ];
            }
        }

        $this->table(['Type', 'Count'], $dbData);

        $this->line('');
        $this->line('<fg=cyan>Health Check</>');
        $this->line('');

        $health = $service->checkStorageHealth();
        
        $statusColor = match ($health['status']) {
            'healthy' => 'green',
            'warning' => 'yellow',
            'critical' => 'red',
            default => 'white',
        };

        $this->line("Status: <fg={$statusColor}>" . strtoupper($health['status']) . '</>' );

        if (!empty($health['warnings'])) {
            $this->line('');
            $this->warn('Warnings:');
            foreach ($health['warnings'] as $warning) {
                $this->line("  - {$warning['message']}");
            }
        }

        if (!empty($health['issues'])) {
            $this->line('');
            $this->error('Issues:');
            foreach ($health['issues'] as $issue) {
                $this->line("  - {$issue['message']}");
            }
        }

        $this->line('');
        $this->line("Generated at: {$stats['generated_at']}");

        return Command::SUCCESS;
    }

    /**
     * Format bytes to human readable format.
     *
     * @param int $bytes
     * @return string
     */
    protected function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
