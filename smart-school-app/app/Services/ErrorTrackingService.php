<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Error Tracking Service
 * 
 * Prompt 505: Create Error Tracking Service
 * 
 * Tracks and manages application errors for monitoring and debugging.
 */
class ErrorTrackingService
{
    /**
     * Track an error.
     */
    public function trackError(Throwable $exception, array $context = []): string
    {
        $errorId = $this->generateErrorId();

        $errorData = [
            'id' => $errorId,
            'type' => get_class($exception),
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $this->formatTrace($exception->getTrace()),
            'context' => $context,
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'user_id' => auth()->id(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toIso8601String(),
        ];

        $this->storeError($errorData);
        $this->logError($errorData);

        return $errorId;
    }

    /**
     * Generate unique error ID.
     */
    protected function generateErrorId(): string
    {
        return 'ERR-' . strtoupper(substr(md5(uniqid()), 0, 8));
    }

    /**
     * Format stack trace.
     */
    protected function formatTrace(array $trace): array
    {
        $formatted = [];

        foreach (array_slice($trace, 0, 10) as $frame) {
            $formatted[] = [
                'file' => $frame['file'] ?? 'unknown',
                'line' => $frame['line'] ?? 0,
                'function' => $frame['function'] ?? 'unknown',
                'class' => $frame['class'] ?? null,
            ];
        }

        return $formatted;
    }

    /**
     * Store error in cache.
     */
    protected function storeError(array $errorData): void
    {
        $key = 'errors:' . date('Y-m-d');
        $errors = Cache::get($key, []);

        $errors[] = $errorData;

        // Keep only last 500 errors per day
        if (count($errors) > 500) {
            $errors = array_slice($errors, -500);
        }

        Cache::put($key, $errors, 86400 * 7); // Keep for 7 days
    }

    /**
     * Log error.
     */
    protected function logError(array $errorData): void
    {
        Log::error('Application error tracked', [
            'error_id' => $errorData['id'],
            'type' => $errorData['type'],
            'message' => $errorData['message'],
            'file' => $errorData['file'],
            'line' => $errorData['line'],
        ]);
    }

    /**
     * Get errors for a date.
     */
    public function getErrors(?string $date = null): array
    {
        $date = $date ?? date('Y-m-d');
        return Cache::get('errors:' . $date, []);
    }

    /**
     * Get error by ID.
     */
    public function getError(string $errorId): ?array
    {
        // Search in last 7 days
        for ($i = 0; $i < 7; $i++) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $errors = $this->getErrors($date);

            foreach ($errors as $error) {
                if ($error['id'] === $errorId) {
                    return $error;
                }
            }
        }

        return null;
    }

    /**
     * Get error summary.
     */
    public function getSummary(?string $date = null): array
    {
        $errors = $this->getErrors($date);

        $summary = [
            'total' => count($errors),
            'by_type' => [],
            'by_file' => [],
            'recent' => array_slice(array_reverse($errors), 0, 10),
        ];

        foreach ($errors as $error) {
            $type = $error['type'];
            $file = basename($error['file']);

            $summary['by_type'][$type] = ($summary['by_type'][$type] ?? 0) + 1;
            $summary['by_file'][$file] = ($summary['by_file'][$file] ?? 0) + 1;
        }

        arsort($summary['by_type']);
        arsort($summary['by_file']);

        return $summary;
    }

    /**
     * Clear errors for a date.
     */
    public function clearErrors(?string $date = null): void
    {
        $date = $date ?? date('Y-m-d');
        Cache::forget('errors:' . $date);
    }

    /**
     * Get error trends.
     */
    public function getTrends(int $days = 7): array
    {
        $trends = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $errors = $this->getErrors($date);

            $trends[] = [
                'date' => $date,
                'count' => count($errors),
            ];
        }

        return $trends;
    }
}
