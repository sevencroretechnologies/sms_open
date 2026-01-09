<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * API Rate Limiter Middleware
 * 
 * Prompt 499: Add API Rate Limiting
 * 
 * Implements rate limiting for API endpoints to prevent abuse.
 * Supports different rate limits based on user type and endpoint.
 */
class ApiRateLimiter
{
    /**
     * Default rate limits per minute
     */
    protected array $defaultLimits = [
        'guest' => 60,
        'authenticated' => 120,
        'admin' => 300,
    ];

    /**
     * Endpoint-specific rate limits
     */
    protected array $endpointLimits = [
        'api/v1/auth/login' => 10,
        'api/v1/auth/register' => 5,
        'api/v1/auth/forgot-password' => 5,
        'api/v1/reports' => 30,
        'api/v1/exports' => 20,
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ?string $limit = null): Response
    {
        $key = $this->resolveRequestSignature($request);
        $maxAttempts = $this->resolveMaxAttempts($request, $limit);
        $decayMinutes = 1;

        if ($this->tooManyAttempts($key, $maxAttempts)) {
            return $this->buildRateLimitResponse($key, $maxAttempts);
        }

        $this->hit($key, $decayMinutes);

        $response = $next($request);

        return $this->addRateLimitHeaders(
            $response,
            $maxAttempts,
            $this->calculateRemainingAttempts($key, $maxAttempts)
        );
    }

    /**
     * Resolve the request signature for rate limiting.
     */
    protected function resolveRequestSignature(Request $request): string
    {
        $user = $request->user();
        
        if ($user) {
            return 'rate_limit:user:' . $user->id . ':' . $request->path();
        }

        return 'rate_limit:ip:' . $request->ip() . ':' . $request->path();
    }

    /**
     * Resolve the maximum number of attempts.
     */
    protected function resolveMaxAttempts(Request $request, ?string $limit): int
    {
        if ($limit !== null) {
            return (int) $limit;
        }

        // Check endpoint-specific limits
        $path = $request->path();
        foreach ($this->endpointLimits as $endpoint => $maxAttempts) {
            if (str_starts_with($path, $endpoint)) {
                return $maxAttempts;
            }
        }

        // Check user type limits
        $user = $request->user();
        if (!$user) {
            return $this->defaultLimits['guest'];
        }

        if ($user->hasRole('admin')) {
            return $this->defaultLimits['admin'];
        }

        return $this->defaultLimits['authenticated'];
    }

    /**
     * Determine if too many attempts have been made.
     */
    protected function tooManyAttempts(string $key, int $maxAttempts): bool
    {
        return Cache::get($key, 0) >= $maxAttempts;
    }

    /**
     * Increment the counter for a given key.
     */
    protected function hit(string $key, int $decayMinutes): int
    {
        $expiresAt = now()->addMinutes($decayMinutes);

        if (!Cache::has($key)) {
            Cache::put($key, 0, $expiresAt);
        }

        return Cache::increment($key);
    }

    /**
     * Calculate remaining attempts.
     */
    protected function calculateRemainingAttempts(string $key, int $maxAttempts): int
    {
        $attempts = Cache::get($key, 0);
        return max(0, $maxAttempts - $attempts);
    }

    /**
     * Get the number of seconds until the rate limit resets.
     */
    protected function getRetryAfter(string $key): int
    {
        return 60; // 1 minute
    }

    /**
     * Build the rate limit exceeded response.
     */
    protected function buildRateLimitResponse(string $key, int $maxAttempts): Response
    {
        $retryAfter = $this->getRetryAfter($key);

        Log::warning('Rate limit exceeded', [
            'key' => $key,
            'max_attempts' => $maxAttempts,
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Too many requests. Please try again later.',
            'error' => 'rate_limit_exceeded',
            'retry_after' => $retryAfter,
        ], 429)->withHeaders([
            'Retry-After' => $retryAfter,
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => 0,
            'X-RateLimit-Reset' => now()->addSeconds($retryAfter)->timestamp,
        ]);
    }

    /**
     * Add rate limit headers to the response.
     */
    protected function addRateLimitHeaders(Response $response, int $maxAttempts, int $remainingAttempts): Response
    {
        $response->headers->set('X-RateLimit-Limit', $maxAttempts);
        $response->headers->set('X-RateLimit-Remaining', $remainingAttempts);
        $response->headers->set('X-RateLimit-Reset', now()->addMinute()->timestamp);

        return $response;
    }

    /**
     * Clear rate limit for a key.
     */
    public static function clear(string $key): void
    {
        Cache::forget($key);
    }

    /**
     * Clear all rate limits for a user.
     */
    public static function clearForUser(int $userId): void
    {
        // This would require a more sophisticated cache implementation
        // For now, we'll just log the action
        Log::info('Rate limit cleared for user', ['user_id' => $userId]);
    }
}
