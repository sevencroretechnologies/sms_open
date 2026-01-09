<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Performance Monitor Service
 * 
 * Prompt 504: Add Performance Monitoring
 * 
 * Monitors application performance including response times,
 * database queries, memory usage, and cache hit rates.
 */
class PerformanceMonitorService
{
    protected array $metrics = [];
    protected float $startTime;
    protected int $startMemory;

    public function __construct()
    {
        $this->startTime = microtime(true);
        $this->startMemory = memory_get_usage();
    }

    /**
     * Start monitoring a request.
     */
    public function startRequest(): void
    {
        $this->startTime = microtime(true);
        $this->startMemory = memory_get_usage();
        $this->metrics = [];

        DB::enableQueryLog();
    }

    /**
     * End monitoring and collect metrics.
     */
    public function endRequest(): array
    {
        $endTime = microtime(true);
        $endMemory = memory_get_usage();

        $this->metrics = [
            'response_time_ms' => round(($endTime - $this->startTime) * 1000, 2),
            'memory_used_mb' => round(($endMemory - $this->startMemory) / 1024 / 1024, 2),
            'peak_memory_mb' => round(memory_get_peak_usage() / 1024 / 1024, 2),
            'queries' => $this->getQueryMetrics(),
            'timestamp' => now()->toIso8601String(),
        ];

        DB::disableQueryLog();

        return $this->metrics;
    }

    /**
     * Get database query metrics.
     */
    protected function getQueryMetrics(): array
    {
        $queries = DB::getQueryLog();
        $totalTime = 0;

        foreach ($queries as $query) {
            $totalTime += $query['time'];
        }

        return [
            'count' => count($queries),
            'total_time_ms' => round($totalTime, 2),
            'slow_queries' => $this->getSlowQueries($queries),
        ];
    }

    /**
     * Get slow queries (over 100ms).
     */
    protected function getSlowQueries(array $queries): array
    {
        $slowQueries = [];

        foreach ($queries as $query) {
            if ($query['time'] > 100) {
                $slowQueries[] = [
                    'sql' => $query['query'],
                    'time_ms' => $query['time'],
                    'bindings' => $query['bindings'],
                ];
            }
        }

        return $slowQueries;
    }

    /**
     * Log performance metrics.
     */
    public function logMetrics(string $endpoint, array $metrics): void
    {
        $logData = array_merge(['endpoint' => $endpoint], $metrics);

        if ($metrics['response_time_ms'] > 1000) {
            Log::warning('Slow response detected', $logData);
        } else {
            Log::info('Performance metrics', $logData);
        }

        $this->storeMetrics($endpoint, $metrics);
    }

    /**
     * Store metrics for analysis.
     */
    protected function storeMetrics(string $endpoint, array $metrics): void
    {
        $key = 'performance_metrics:' . date('Y-m-d');
        $existingMetrics = Cache::get($key, []);

        $existingMetrics[] = [
            'endpoint' => $endpoint,
            'metrics' => $metrics,
        ];

        // Keep only last 1000 entries per day
        if (count($existingMetrics) > 1000) {
            $existingMetrics = array_slice($existingMetrics, -1000);
        }

        Cache::put($key, $existingMetrics, 86400);
    }

    /**
     * Get performance summary for a date.
     */
    public function getSummary(?string $date = null): array
    {
        $date = $date ?? date('Y-m-d');
        $key = 'performance_metrics:' . $date;
        $metrics = Cache::get($key, []);

        if (empty($metrics)) {
            return [
                'date' => $date,
                'total_requests' => 0,
                'avg_response_time_ms' => 0,
                'max_response_time_ms' => 0,
                'avg_memory_mb' => 0,
                'total_queries' => 0,
                'slow_requests' => 0,
            ];
        }

        $responseTimes = [];
        $memoryUsage = [];
        $queryCount = 0;
        $slowRequests = 0;

        foreach ($metrics as $entry) {
            $responseTimes[] = $entry['metrics']['response_time_ms'];
            $memoryUsage[] = $entry['metrics']['memory_used_mb'];
            $queryCount += $entry['metrics']['queries']['count'];

            if ($entry['metrics']['response_time_ms'] > 1000) {
                $slowRequests++;
            }
        }

        return [
            'date' => $date,
            'total_requests' => count($metrics),
            'avg_response_time_ms' => round(array_sum($responseTimes) / count($responseTimes), 2),
            'max_response_time_ms' => max($responseTimes),
            'min_response_time_ms' => min($responseTimes),
            'avg_memory_mb' => round(array_sum($memoryUsage) / count($memoryUsage), 2),
            'total_queries' => $queryCount,
            'avg_queries_per_request' => round($queryCount / count($metrics), 2),
            'slow_requests' => $slowRequests,
            'slow_request_percentage' => round(($slowRequests / count($metrics)) * 100, 2),
        ];
    }

    /**
     * Get endpoint statistics.
     */
    public function getEndpointStats(?string $date = null): array
    {
        $date = $date ?? date('Y-m-d');
        $key = 'performance_metrics:' . $date;
        $metrics = Cache::get($key, []);

        $endpointStats = [];

        foreach ($metrics as $entry) {
            $endpoint = $entry['endpoint'];

            if (!isset($endpointStats[$endpoint])) {
                $endpointStats[$endpoint] = [
                    'count' => 0,
                    'total_time' => 0,
                    'max_time' => 0,
                ];
            }

            $endpointStats[$endpoint]['count']++;
            $endpointStats[$endpoint]['total_time'] += $entry['metrics']['response_time_ms'];
            $endpointStats[$endpoint]['max_time'] = max(
                $endpointStats[$endpoint]['max_time'],
                $entry['metrics']['response_time_ms']
            );
        }

        // Calculate averages
        foreach ($endpointStats as $endpoint => &$stats) {
            $stats['avg_time'] = round($stats['total_time'] / $stats['count'], 2);
            unset($stats['total_time']);
        }

        // Sort by count descending
        uasort($endpointStats, function ($a, $b) {
            return $b['count'] - $a['count'];
        });

        return $endpointStats;
    }

    /**
     * Check system health.
     */
    public function checkHealth(): array
    {
        $health = [
            'status' => 'healthy',
            'checks' => [],
        ];

        // Check database connection
        try {
            DB::connection()->getPdo();
            $health['checks']['database'] = ['status' => 'ok'];
        } catch (\Exception $e) {
            $health['checks']['database'] = ['status' => 'error', 'message' => $e->getMessage()];
            $health['status'] = 'unhealthy';
        }

        // Check cache connection
        try {
            Cache::put('health_check', true, 1);
            Cache::forget('health_check');
            $health['checks']['cache'] = ['status' => 'ok'];
        } catch (\Exception $e) {
            $health['checks']['cache'] = ['status' => 'error', 'message' => $e->getMessage()];
            $health['status'] = 'unhealthy';
        }

        // Check disk space
        $freeSpace = disk_free_space('/');
        $totalSpace = disk_total_space('/');
        $usedPercentage = round((1 - $freeSpace / $totalSpace) * 100, 2);

        $health['checks']['disk'] = [
            'status' => $usedPercentage > 90 ? 'warning' : 'ok',
            'used_percentage' => $usedPercentage,
            'free_gb' => round($freeSpace / 1024 / 1024 / 1024, 2),
        ];

        if ($usedPercentage > 95) {
            $health['status'] = 'unhealthy';
        }

        // Check memory
        $memoryLimit = ini_get('memory_limit');
        $memoryUsage = memory_get_usage(true);
        $health['checks']['memory'] = [
            'status' => 'ok',
            'limit' => $memoryLimit,
            'usage_mb' => round($memoryUsage / 1024 / 1024, 2),
        ];

        return $health;
    }
}
