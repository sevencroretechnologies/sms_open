<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PerformanceMonitorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

/**
 * Health Check Controller
 * 
 * Prompt 509: Add Health Check Endpoints
 * 
 * Provides health check endpoints for monitoring application status.
 */
class HealthController extends Controller
{
    protected PerformanceMonitorService $performanceMonitor;

    public function __construct(PerformanceMonitorService $performanceMonitor)
    {
        $this->performanceMonitor = $performanceMonitor;
    }

    /**
     * Basic health check.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now()->toIso8601String(),
            'version' => config('app.version', '1.0.0'),
        ]);
    }

    /**
     * Detailed health check.
     */
    public function detailed(): JsonResponse
    {
        $health = $this->performanceMonitor->checkHealth();

        $statusCode = $health['status'] === 'healthy' ? 200 : 503;

        return response()->json($health, $statusCode);
    }

    /**
     * Database health check.
     */
    public function database(): JsonResponse
    {
        try {
            $start = microtime(true);
            DB::connection()->getPdo();
            $latency = round((microtime(true) - $start) * 1000, 2);

            return response()->json([
                'status' => 'ok',
                'driver' => config('database.default'),
                'latency_ms' => $latency,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 503);
        }
    }

    /**
     * Cache health check.
     */
    public function cache(): JsonResponse
    {
        try {
            $start = microtime(true);
            $key = 'health_check_' . uniqid();
            Cache::put($key, true, 10);
            $value = Cache::get($key);
            Cache::forget($key);
            $latency = round((microtime(true) - $start) * 1000, 2);

            if ($value !== true) {
                throw new \Exception('Cache read/write failed');
            }

            return response()->json([
                'status' => 'ok',
                'driver' => config('cache.default'),
                'latency_ms' => $latency,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 503);
        }
    }

    /**
     * Storage health check.
     */
    public function storage(): JsonResponse
    {
        try {
            $disk = Storage::disk('local');
            $testFile = 'health_check_' . uniqid() . '.txt';
            
            $disk->put($testFile, 'test');
            $content = $disk->get($testFile);
            $disk->delete($testFile);

            if ($content !== 'test') {
                throw new \Exception('Storage read/write failed');
            }

            return response()->json([
                'status' => 'ok',
                'disk' => 'local',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 503);
        }
    }

    /**
     * Queue health check.
     */
    public function queue(): JsonResponse
    {
        try {
            $connection = config('queue.default');

            return response()->json([
                'status' => 'ok',
                'driver' => $connection,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 503);
        }
    }

    /**
     * System metrics.
     */
    public function metrics(): JsonResponse
    {
        $summary = $this->performanceMonitor->getSummary();

        return response()->json([
            'status' => 'ok',
            'metrics' => $summary,
            'system' => [
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
                'memory_limit' => ini_get('memory_limit'),
                'max_execution_time' => ini_get('max_execution_time'),
            ],
        ]);
    }

    /**
     * Readiness check (for Kubernetes).
     */
    public function ready(): JsonResponse
    {
        $checks = [
            'database' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
        ];

        $allReady = !in_array(false, $checks, true);

        return response()->json([
            'ready' => $allReady,
            'checks' => $checks,
        ], $allReady ? 200 : 503);
    }

    /**
     * Liveness check (for Kubernetes).
     */
    public function live(): JsonResponse
    {
        return response()->json([
            'alive' => true,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Check database connection.
     */
    protected function checkDatabase(): bool
    {
        try {
            DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check cache connection.
     */
    protected function checkCache(): bool
    {
        try {
            Cache::put('health_check', true, 1);
            Cache::forget('health_check');
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
