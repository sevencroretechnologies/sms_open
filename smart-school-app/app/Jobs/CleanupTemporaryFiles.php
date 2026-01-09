<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * Cleanup Temporary Files Job
 * 
 * Prompt 413: Create Temporary File Cleanup Job
 * 
 * Scans temporary storage directories and removes files older than
 * the configured retention period. Runs as a queued job for
 * non-blocking execution.
 * 
 * Features:
 * - Scans configured temp directories
 * - Removes files older than retention period
 * - Logs cleanup results for auditing
 * - Handles errors gracefully without stopping the job
 */
class CleanupTemporaryFiles implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $maxAgeDays;
    protected array $directories;

    /**
     * Create a new job instance.
     *
     * @param int|null $maxAgeDays Override default max age in days
     * @param array|null $directories Override default directories to clean
     */
    public function __construct(?int $maxAgeDays = null, ?array $directories = null)
    {
        $this->maxAgeDays = $maxAgeDays ?? config('cleanup.temp_files.max_age_days', 7);
        $this->directories = $directories ?? config('cleanup.temp_files.directories', ['temp']);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $startTime = microtime(true);
        $totalDeleted = 0;
        $totalSize = 0;
        $errors = [];

        Log::info('Starting temporary file cleanup job', [
            'max_age_days' => $this->maxAgeDays,
            'directories' => $this->directories,
        ]);

        foreach ($this->directories as $directory) {
            try {
                $result = $this->cleanDirectory($directory);
                $totalDeleted += $result['deleted'];
                $totalSize += $result['size'];
                
                if (!empty($result['errors'])) {
                    $errors = array_merge($errors, $result['errors']);
                }
            } catch (\Exception $e) {
                $errors[] = "Error cleaning directory {$directory}: " . $e->getMessage();
                Log::error('Error cleaning temporary directory', [
                    'directory' => $directory,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $duration = round(microtime(true) - $startTime, 2);

        Log::info('Temporary file cleanup completed', [
            'files_deleted' => $totalDeleted,
            'space_freed_bytes' => $totalSize,
            'space_freed_mb' => round($totalSize / 1024 / 1024, 2),
            'duration_seconds' => $duration,
            'errors_count' => count($errors),
        ]);

        if (!empty($errors)) {
            Log::warning('Temporary file cleanup completed with errors', [
                'errors' => $errors,
            ]);
        }
    }

    /**
     * Clean a specific directory of old files.
     *
     * @param string $directory
     * @return array
     */
    protected function cleanDirectory(string $directory): array
    {
        $deleted = 0;
        $size = 0;
        $errors = [];
        $cutoffDate = Carbon::now()->subDays($this->maxAgeDays);

        $disks = ['public_uploads', 'private_uploads', 'local'];

        foreach ($disks as $disk) {
            try {
                if (!Storage::disk($disk)->exists($directory)) {
                    continue;
                }

                $files = Storage::disk($disk)->files($directory);

                foreach ($files as $file) {
                    try {
                        $lastModified = Storage::disk($disk)->lastModified($file);
                        $fileDate = Carbon::createFromTimestamp($lastModified);

                        if ($fileDate->lt($cutoffDate)) {
                            $fileSize = Storage::disk($disk)->size($file);
                            
                            if (Storage::disk($disk)->delete($file)) {
                                $deleted++;
                                $size += $fileSize;
                                
                                Log::debug('Deleted temporary file', [
                                    'file' => $file,
                                    'disk' => $disk,
                                    'age_days' => $fileDate->diffInDays(Carbon::now()),
                                    'size_bytes' => $fileSize,
                                ]);
                            }
                        }
                    } catch (\Exception $e) {
                        $errors[] = "Error deleting file {$file}: " . $e->getMessage();
                    }
                }

                $subdirectories = Storage::disk($disk)->directories($directory);
                foreach ($subdirectories as $subdir) {
                    $subResult = $this->cleanDirectory($subdir);
                    $deleted += $subResult['deleted'];
                    $size += $subResult['size'];
                    $errors = array_merge($errors, $subResult['errors']);
                }
            } catch (\Exception $e) {
                $errors[] = "Error accessing disk {$disk}: " . $e->getMessage();
            }
        }

        return [
            'deleted' => $deleted,
            'size' => $size,
            'errors' => $errors,
        ];
    }

    /**
     * Get the tags that should be assigned to the job.
     *
     * @return array
     */
    public function tags(): array
    {
        return ['cleanup', 'temporary-files'];
    }

    /**
     * Determine the time at which the job should timeout.
     *
     * @return \DateTime
     */
    public function retryUntil(): \DateTime
    {
        return now()->addMinutes(30);
    }

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = 60;
}
