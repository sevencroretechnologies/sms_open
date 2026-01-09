<?php

/**
 * File Cleanup Configuration
 * 
 * Prompt 418: Create Cleanup Configuration
 * 
 * Configuration for temporary file cleanup and orphaned file detection.
 * Used by CleanupTemporaryFiles and CleanupOrphanedFiles jobs.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Temporary File Cleanup Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for cleaning up temporary files that are no longer needed.
    |
    */

    'temp_files' => [
        'enabled' => env('CLEANUP_TEMP_ENABLED', true),
        'max_age_days' => env('CLEANUP_TEMP_MAX_AGE_DAYS', 7),
        'directories' => [
            'temp',
            'tmp',
            'cache/files',
            'exports/temp',
            'imports/temp',
        ],
        'schedule' => [
            'frequency' => 'daily',
            'time' => '02:00',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Orphaned File Cleanup Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for cleaning up files that are no longer referenced
    | in the database.
    |
    */

    'orphan_files' => [
        'enabled' => env('CLEANUP_ORPHAN_ENABLED', true),
        'directories' => [
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
            'hostel/rooms',
            'transport/documents',
        ],
        'exclude_patterns' => [
            '.gitkeep',
            '.gitignore',
            'README.md',
            '*.placeholder',
        ],
        'schedule' => [
            'frequency' => 'weekly',
            'day' => 'sunday',
            'time' => '03:00',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Export File Cleanup Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for cleaning up generated export files.
    |
    */

    'exports' => [
        'enabled' => env('CLEANUP_EXPORTS_ENABLED', true),
        'max_age_days' => env('CLEANUP_EXPORTS_MAX_AGE_DAYS', 30),
        'directories' => [
            'exports',
            'reports',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Import File Cleanup Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for cleaning up uploaded import files after processing.
    |
    */

    'imports' => [
        'enabled' => env('CLEANUP_IMPORTS_ENABLED', true),
        'max_age_days' => env('CLEANUP_IMPORTS_MAX_AGE_DAYS', 7),
        'directories' => [
            'imports',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for cleanup job logging.
    |
    */

    'logging' => [
        'enabled' => env('CLEANUP_LOGGING_ENABLED', true),
        'channel' => env('CLEANUP_LOG_CHANNEL', 'daily'),
        'log_deleted_files' => env('CLEANUP_LOG_DELETED_FILES', true),
        'log_errors' => env('CLEANUP_LOG_ERRORS', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Storage Thresholds
    |--------------------------------------------------------------------------
    |
    | Thresholds for storage warnings and alerts.
    |
    */

    'thresholds' => [
        'warning_percentage' => env('STORAGE_WARNING_PERCENTAGE', 80),
        'critical_percentage' => env('STORAGE_CRITICAL_PERCENTAGE', 95),
        'max_file_size_mb' => env('MAX_FILE_SIZE_MB', 50),
        'orphan_ratio_warning' => env('ORPHAN_RATIO_WARNING', 1.2),
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for cleanup notifications.
    |
    */

    'notifications' => [
        'enabled' => env('CLEANUP_NOTIFICATIONS_ENABLED', false),
        'channels' => ['mail'],
        'recipients' => [
            // Add admin email addresses here
        ],
        'notify_on_error' => true,
        'notify_on_warning' => true,
        'notify_on_completion' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Safety Settings
    |--------------------------------------------------------------------------
    |
    | Safety measures to prevent accidental data loss.
    |
    */

    'safety' => [
        'dry_run_by_default' => env('CLEANUP_DRY_RUN', false),
        'require_confirmation' => env('CLEANUP_REQUIRE_CONFIRMATION', false),
        'backup_before_delete' => env('CLEANUP_BACKUP_BEFORE_DELETE', false),
        'max_files_per_run' => env('CLEANUP_MAX_FILES_PER_RUN', 1000),
        'protected_directories' => [
            'system',
            'framework',
            'logs',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for cleanup job queue behavior.
    |
    */

    'queue' => [
        'connection' => env('CLEANUP_QUEUE_CONNECTION', 'default'),
        'queue_name' => env('CLEANUP_QUEUE_NAME', 'cleanup'),
        'timeout' => env('CLEANUP_TIMEOUT', 3600),
        'tries' => env('CLEANUP_TRIES', 3),
        'backoff' => env('CLEANUP_BACKOFF', 60),
    ],

];
