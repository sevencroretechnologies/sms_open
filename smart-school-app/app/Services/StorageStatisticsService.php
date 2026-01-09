<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
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
 * Storage Statistics Service
 * 
 * Prompt 415: Create Storage Statistics Service
 * 
 * Provides storage usage statistics and analytics for file management.
 * Tracks storage usage by module, file type, and time period.
 * 
 * Features:
 * - Calculate total storage usage by disk
 * - Break down storage by module/directory
 * - Track file counts and sizes
 * - Identify largest files and directories
 * - Generate storage reports
 */
class StorageStatisticsService
{
    /**
     * Get overall storage statistics.
     *
     * @return array
     */
    public function getOverallStatistics(): array
    {
        $publicStats = $this->getDiskStatistics('public_uploads');
        $privateStats = $this->getDiskStatistics('private_uploads');

        return [
            'public_storage' => $publicStats,
            'private_storage' => $privateStats,
            'total' => [
                'files' => $publicStats['files'] + $privateStats['files'],
                'size_bytes' => $publicStats['size_bytes'] + $privateStats['size_bytes'],
                'size_formatted' => $this->formatBytes($publicStats['size_bytes'] + $privateStats['size_bytes']),
            ],
            'generated_at' => now()->toIso8601String(),
        ];
    }

    /**
     * Get statistics for a specific disk.
     *
     * @param string $disk
     * @return array
     */
    public function getDiskStatistics(string $disk): array
    {
        $totalFiles = 0;
        $totalSize = 0;
        $directories = [];

        try {
            $allDirectories = Storage::disk($disk)->directories();
            
            foreach ($allDirectories as $directory) {
                $dirStats = $this->getDirectoryStatistics($disk, $directory);
                $directories[$directory] = $dirStats;
                $totalFiles += $dirStats['files'];
                $totalSize += $dirStats['size_bytes'];
            }

            $rootFiles = Storage::disk($disk)->files();
            foreach ($rootFiles as $file) {
                $totalFiles++;
                $totalSize += Storage::disk($disk)->size($file);
            }
        } catch (\Exception $e) {
            return [
                'files' => 0,
                'size_bytes' => 0,
                'size_formatted' => '0 B',
                'directories' => [],
                'error' => $e->getMessage(),
            ];
        }

        return [
            'files' => $totalFiles,
            'size_bytes' => $totalSize,
            'size_formatted' => $this->formatBytes($totalSize),
            'directories' => $directories,
        ];
    }

    /**
     * Get statistics for a specific directory.
     *
     * @param string $disk
     * @param string $directory
     * @return array
     */
    public function getDirectoryStatistics(string $disk, string $directory): array
    {
        $files = 0;
        $size = 0;
        $fileTypes = [];

        try {
            $allFiles = Storage::disk($disk)->allFiles($directory);
            
            foreach ($allFiles as $file) {
                $files++;
                $fileSize = Storage::disk($disk)->size($file);
                $size += $fileSize;

                $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                if (!isset($fileTypes[$extension])) {
                    $fileTypes[$extension] = ['count' => 0, 'size' => 0];
                }
                $fileTypes[$extension]['count']++;
                $fileTypes[$extension]['size'] += $fileSize;
            }
        } catch (\Exception $e) {
            return [
                'files' => 0,
                'size_bytes' => 0,
                'size_formatted' => '0 B',
                'file_types' => [],
                'error' => $e->getMessage(),
            ];
        }

        return [
            'files' => $files,
            'size_bytes' => $size,
            'size_formatted' => $this->formatBytes($size),
            'file_types' => $fileTypes,
        ];
    }

    /**
     * Get storage statistics by module.
     *
     * @return array
     */
    public function getModuleStatistics(): array
    {
        $modules = [
            'students' => [
                'photos' => $this->getDirectoryStats('public_uploads', 'students/photos'),
                'documents' => $this->getDirectoryStats('private_uploads', 'students/documents'),
            ],
            'teachers' => [
                'photos' => $this->getDirectoryStats('public_uploads', 'teachers/photos'),
                'documents' => $this->getDirectoryStats('private_uploads', 'teachers/documents'),
            ],
            'library' => [
                'covers' => $this->getDirectoryStats('public_uploads', 'library/covers'),
            ],
            'homework' => [
                'attachments' => $this->getDirectoryStats('private_uploads', 'homework'),
            ],
            'study_materials' => [
                'files' => $this->getDirectoryStats('private_uploads', 'study_materials'),
            ],
            'communications' => [
                'notices' => $this->getDirectoryStats('private_uploads', 'communications/notice'),
                'messages' => $this->getDirectoryStats('private_uploads', 'communications/message'),
            ],
            'fees' => [
                'proofs' => $this->getDirectoryStats('private_uploads', 'fees/proofs'),
            ],
            'hostel' => [
                'images' => $this->getDirectoryStats('public_uploads', 'hostel/rooms'),
            ],
            'transport' => [
                'documents' => $this->getDirectoryStats('private_uploads', 'transport/documents'),
            ],
        ];

        $totalFiles = 0;
        $totalSize = 0;

        foreach ($modules as $module => $categories) {
            foreach ($categories as $category => $stats) {
                $totalFiles += $stats['files'];
                $totalSize += $stats['size_bytes'];
            }
        }

        return [
            'modules' => $modules,
            'total' => [
                'files' => $totalFiles,
                'size_bytes' => $totalSize,
                'size_formatted' => $this->formatBytes($totalSize),
            ],
            'generated_at' => now()->toIso8601String(),
        ];
    }

    /**
     * Get directory stats helper.
     *
     * @param string $disk
     * @param string $directory
     * @return array
     */
    protected function getDirectoryStats(string $disk, string $directory): array
    {
        try {
            if (!Storage::disk($disk)->exists($directory)) {
                return [
                    'files' => 0,
                    'size_bytes' => 0,
                    'size_formatted' => '0 B',
                ];
            }

            return $this->getDirectoryStatistics($disk, $directory);
        } catch (\Exception $e) {
            return [
                'files' => 0,
                'size_bytes' => 0,
                'size_formatted' => '0 B',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get database file reference statistics.
     *
     * @return array
     */
    public function getDatabaseStatistics(): array
    {
        return [
            'student_photos' => Student::whereNotNull('photo')->count(),
            'student_documents' => StudentDocument::count(),
            'teacher_documents' => TeacherDocument::count(),
            'homework_attachments' => HomeworkAttachment::count(),
            'study_material_attachments' => StudyMaterialAttachment::count(),
            'assignment_submissions' => AssignmentSubmission::count(),
            'notice_attachments' => NoticeAttachment::count(),
            'message_attachments' => MessageAttachment::count(),
            'payment_proofs' => PaymentProof::count(),
            'book_covers' => LibraryBook::whereNotNull('cover_image')->count(),
            'generated_at' => now()->toIso8601String(),
        ];
    }

    /**
     * Get the largest files in storage.
     *
     * @param int $limit
     * @return array
     */
    public function getLargestFiles(int $limit = 20): array
    {
        $files = [];
        $disks = ['public_uploads', 'private_uploads'];

        foreach ($disks as $disk) {
            try {
                $allFiles = Storage::disk($disk)->allFiles();
                
                foreach ($allFiles as $file) {
                    $size = Storage::disk($disk)->size($file);
                    $files[] = [
                        'path' => $file,
                        'disk' => $disk,
                        'size_bytes' => $size,
                        'size_formatted' => $this->formatBytes($size),
                        'last_modified' => date('Y-m-d H:i:s', Storage::disk($disk)->lastModified($file)),
                    ];
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        usort($files, function ($a, $b) {
            return $b['size_bytes'] <=> $a['size_bytes'];
        });

        return array_slice($files, 0, $limit);
    }

    /**
     * Get storage usage trend (requires historical data).
     *
     * @param int $days
     * @return array
     */
    public function getStorageTrend(int $days = 30): array
    {
        $trend = [];
        $startDate = now()->subDays($days);

        $studentDocs = StudentDocument::where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(file_size) as total_size')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        foreach ($studentDocs as $doc) {
            if (!isset($trend[$doc->date])) {
                $trend[$doc->date] = ['files' => 0, 'size' => 0];
            }
            $trend[$doc->date]['files'] += $doc->count;
            $trend[$doc->date]['size'] += $doc->total_size ?? 0;
        }

        return [
            'period_days' => $days,
            'start_date' => $startDate->toDateString(),
            'end_date' => now()->toDateString(),
            'daily_data' => $trend,
        ];
    }

    /**
     * Get file type distribution.
     *
     * @return array
     */
    public function getFileTypeDistribution(): array
    {
        $distribution = [];
        $disks = ['public_uploads', 'private_uploads'];

        foreach ($disks as $disk) {
            try {
                $allFiles = Storage::disk($disk)->allFiles();
                
                foreach ($allFiles as $file) {
                    $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    if (empty($extension)) {
                        $extension = 'unknown';
                    }

                    if (!isset($distribution[$extension])) {
                        $distribution[$extension] = [
                            'count' => 0,
                            'size_bytes' => 0,
                        ];
                    }

                    $distribution[$extension]['count']++;
                    $distribution[$extension]['size_bytes'] += Storage::disk($disk)->size($file);
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        foreach ($distribution as $ext => $data) {
            $distribution[$ext]['size_formatted'] = $this->formatBytes($data['size_bytes']);
        }

        arsort($distribution);

        return $distribution;
    }

    /**
     * Check for potential storage issues.
     *
     * @return array
     */
    public function checkStorageHealth(): array
    {
        $issues = [];
        $warnings = [];

        $stats = $this->getOverallStatistics();
        $dbStats = $this->getDatabaseStatistics();

        $totalDbReferences = array_sum(array_filter($dbStats, fn($v) => is_int($v)));
        $totalFiles = $stats['total']['files'];

        if ($totalFiles > $totalDbReferences * 1.2) {
            $warnings[] = [
                'type' => 'orphaned_files',
                'message' => 'Storage contains more files than database references. Consider running orphan cleanup.',
                'storage_files' => $totalFiles,
                'db_references' => $totalDbReferences,
            ];
        }

        $largeFiles = $this->getLargestFiles(5);
        foreach ($largeFiles as $file) {
            if ($file['size_bytes'] > 50 * 1024 * 1024) {
                $warnings[] = [
                    'type' => 'large_file',
                    'message' => "Large file detected: {$file['path']} ({$file['size_formatted']})",
                    'file' => $file,
                ];
            }
        }

        return [
            'status' => empty($issues) ? (empty($warnings) ? 'healthy' : 'warning') : 'critical',
            'issues' => $issues,
            'warnings' => $warnings,
            'checked_at' => now()->toIso8601String(),
        ];
    }

    /**
     * Format bytes to human readable format.
     *
     * @param int $bytes
     * @param int $precision
     * @return string
     */
    protected function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Generate a comprehensive storage report.
     *
     * @return array
     */
    public function generateReport(): array
    {
        return [
            'overall' => $this->getOverallStatistics(),
            'by_module' => $this->getModuleStatistics(),
            'database_references' => $this->getDatabaseStatistics(),
            'file_types' => $this->getFileTypeDistribution(),
            'largest_files' => $this->getLargestFiles(10),
            'health_check' => $this->checkStorageHealth(),
            'generated_at' => now()->toIso8601String(),
        ];
    }
}
