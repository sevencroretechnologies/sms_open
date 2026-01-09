<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Student;
use App\Models\StudentDocument;
use App\Models\TeacherDocument;
use App\Models\HomeworkAttachment;
use App\Models\StudyMaterialAttachment;
use App\Models\AssignmentSubmission;
use App\Models\NoticeAttachment;
use App\Models\MessageAttachment;
use App\Models\PaymentProof;
use App\Models\LibraryBook;

/**
 * Cleanup Orphaned Files Job
 * 
 * Prompt 414: Create Orphaned File Cleanup Job
 * 
 * Scans storage directories and compares file references with database
 * records. Removes files that are no longer referenced by any database
 * record (orphaned files).
 * 
 * Features:
 * - Compares storage files with database references
 * - Removes orphaned files not linked to any record
 * - Logs cleanup results for auditing
 * - Handles errors gracefully without stopping the job
 * - Supports dry-run mode for testing
 */
class CleanupOrphanedFiles implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected bool $dryRun;
    protected array $directories;

    /**
     * Create a new job instance.
     *
     * @param bool $dryRun If true, only log what would be deleted without actually deleting
     * @param array|null $directories Override default directories to scan
     */
    public function __construct(bool $dryRun = false, ?array $directories = null)
    {
        $this->dryRun = $dryRun;
        $this->directories = $directories ?? config('cleanup.orphan_files.directories', [
            'students/photos',
            'students/documents',
            'teachers/photos',
            'teachers/documents',
            'homework',
            'study_materials',
            'communications/notice',
            'communications/message',
            'fees/proofs',
            'library/covers',
        ]);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $startTime = microtime(true);
        $totalOrphaned = 0;
        $totalSize = 0;
        $errors = [];

        Log::info('Starting orphaned file cleanup job', [
            'dry_run' => $this->dryRun,
            'directories' => $this->directories,
        ]);

        $referencedFiles = $this->getAllReferencedFiles();

        foreach ($this->directories as $directory) {
            try {
                $result = $this->scanDirectory($directory, $referencedFiles);
                $totalOrphaned += $result['orphaned'];
                $totalSize += $result['size'];
                
                if (!empty($result['errors'])) {
                    $errors = array_merge($errors, $result['errors']);
                }
            } catch (\Exception $e) {
                $errors[] = "Error scanning directory {$directory}: " . $e->getMessage();
                Log::error('Error scanning directory for orphaned files', [
                    'directory' => $directory,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $duration = round(microtime(true) - $startTime, 2);

        Log::info('Orphaned file cleanup completed', [
            'dry_run' => $this->dryRun,
            'orphaned_files_found' => $totalOrphaned,
            'space_freed_bytes' => $totalSize,
            'space_freed_mb' => round($totalSize / 1024 / 1024, 2),
            'duration_seconds' => $duration,
            'errors_count' => count($errors),
        ]);

        if (!empty($errors)) {
            Log::warning('Orphaned file cleanup completed with errors', [
                'errors' => $errors,
            ]);
        }
    }

    /**
     * Get all file paths referenced in the database.
     *
     * @return array
     */
    protected function getAllReferencedFiles(): array
    {
        $referencedFiles = [];

        $referencedFiles = array_merge(
            $referencedFiles,
            Student::whereNotNull('photo')->pluck('photo')->toArray()
        );

        $referencedFiles = array_merge(
            $referencedFiles,
            StudentDocument::whereNotNull('file_path')->pluck('file_path')->toArray()
        );

        $referencedFiles = array_merge(
            $referencedFiles,
            TeacherDocument::whereNotNull('file_path')->pluck('file_path')->toArray()
        );

        $referencedFiles = array_merge(
            $referencedFiles,
            HomeworkAttachment::whereNotNull('file_path')->pluck('file_path')->toArray()
        );

        $referencedFiles = array_merge(
            $referencedFiles,
            StudyMaterialAttachment::whereNotNull('file_path')->pluck('file_path')->toArray()
        );

        $referencedFiles = array_merge(
            $referencedFiles,
            AssignmentSubmission::whereNotNull('file_path')->pluck('file_path')->toArray()
        );

        $referencedFiles = array_merge(
            $referencedFiles,
            NoticeAttachment::whereNotNull('file_path')->pluck('file_path')->toArray()
        );

        $referencedFiles = array_merge(
            $referencedFiles,
            MessageAttachment::whereNotNull('file_path')->pluck('file_path')->toArray()
        );

        $referencedFiles = array_merge(
            $referencedFiles,
            PaymentProof::whereNotNull('file_path')->pluck('file_path')->toArray()
        );

        $referencedFiles = array_merge(
            $referencedFiles,
            LibraryBook::whereNotNull('cover_image')->pluck('cover_image')->toArray()
        );

        return array_unique($referencedFiles);
    }

    /**
     * Scan a directory for orphaned files.
     *
     * @param string $directory
     * @param array $referencedFiles
     * @return array
     */
    protected function scanDirectory(string $directory, array $referencedFiles): array
    {
        $orphaned = 0;
        $size = 0;
        $errors = [];

        $disks = ['public_uploads', 'private_uploads'];

        foreach ($disks as $disk) {
            try {
                if (!Storage::disk($disk)->exists($directory)) {
                    continue;
                }

                $files = Storage::disk($disk)->files($directory);

                foreach ($files as $file) {
                    try {
                        if (!in_array($file, $referencedFiles)) {
                            $fileSize = Storage::disk($disk)->size($file);
                            
                            if ($this->dryRun) {
                                Log::info('Would delete orphaned file (dry run)', [
                                    'file' => $file,
                                    'disk' => $disk,
                                    'size_bytes' => $fileSize,
                                ]);
                            } else {
                                if (Storage::disk($disk)->delete($file)) {
                                    Log::debug('Deleted orphaned file', [
                                        'file' => $file,
                                        'disk' => $disk,
                                        'size_bytes' => $fileSize,
                                    ]);
                                }
                            }
                            
                            $orphaned++;
                            $size += $fileSize;
                        }
                    } catch (\Exception $e) {
                        $errors[] = "Error processing file {$file}: " . $e->getMessage();
                    }
                }

                $subdirectories = Storage::disk($disk)->directories($directory);
                foreach ($subdirectories as $subdir) {
                    $subResult = $this->scanDirectory($subdir, $referencedFiles);
                    $orphaned += $subResult['orphaned'];
                    $size += $subResult['size'];
                    $errors = array_merge($errors, $subResult['errors']);
                }
            } catch (\Exception $e) {
                $errors[] = "Error accessing disk {$disk}: " . $e->getMessage();
            }
        }

        return [
            'orphaned' => $orphaned,
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
        return ['cleanup', 'orphaned-files'];
    }

    /**
     * Determine the time at which the job should timeout.
     *
     * @return \DateTime
     */
    public function retryUntil(): \DateTime
    {
        return now()->addHours(1);
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
    public $backoff = 120;
}
