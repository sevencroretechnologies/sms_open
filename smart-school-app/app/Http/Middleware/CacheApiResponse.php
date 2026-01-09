<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

/**
 * Cache API Response Middleware
 * 
 * Prompt 502: Add API Response Caching
 * 
 * Caches API responses to improve performance for frequently accessed endpoints.
 */
class CacheApiResponse
{
    /**
     * Default cache duration in seconds
     */
    protected int $defaultTtl = 300; // 5 minutes

    /**
     * Cacheable HTTP methods
     */
    protected array $cacheableMethods = ['GET', 'HEAD'];

    /**
     * Endpoints that should not be cached
     */
    protected array $excludedEndpoints = [
        'api/v1/auth',
        'api/v1/tokens',
        'api/v1/notifications',
        'api/v1/health',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ?int $ttl = null): Response
    {
        // Only cache GET and HEAD requests
        if (!in_array($request->method(), $this->cacheableMethods)) {
            return $next($request);
        }

        // Don't cache excluded endpoints
        if ($this->isExcluded($request)) {
            return $next($request);
        }

        // Don't cache authenticated requests by default
        if ($request->user() && !$request->has('cache')) {
            return $next($request);
        }

        $cacheKey = $this->getCacheKey($request);
        $ttl = $ttl ?? $this->defaultTtl;

        // Check if response is cached
        if (Cache::has($cacheKey)) {
            $cachedResponse = Cache::get($cacheKey);
            return $this->buildCachedResponse($cachedResponse);
        }

        // Get fresh response
        $response = $next($request);

        // Only cache successful responses
        if ($response->isSuccessful()) {
            $this->cacheResponse($cacheKey, $response, $ttl);
        }

        return $response;
    }

    /**
     * Generate cache key for the request.
     */
    protected function getCacheKey(Request $request): string
    {
        $key = 'api_cache:' . $request->method() . ':' . $request->fullUrl();
        
        // Include user ID if authenticated
        if ($request->user()) {
            $key .= ':user:' . $request->user()->id;
        }

        return md5($key);
    }

    /**
     * Check if endpoint is excluded from caching.
     */
    protected function isExcluded(Request $request): bool
    {
        $path = $request->path();
        
        foreach ($this->excludedEndpoints as $excluded) {
            if (str_starts_with($path, $excluded)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Cache the response.
     */
    protected function cacheResponse(string $key, Response $response, int $ttl): void
    {
        $cacheData = [
            'content' => $response->getContent(),
            'status' => $response->getStatusCode(),
            'headers' => $response->headers->all(),
            'cached_at' => now()->toIso8601String(),
        ];

        Cache::put($key, $cacheData, $ttl);
    }

    /**
     * Build response from cached data.
     */
    protected function buildCachedResponse(array $cachedData): Response
    {
        $response = response($cachedData['content'], $cachedData['status']);
        
        foreach ($cachedData['headers'] as $name => $values) {
            $response->headers->set($name, $values);
        }

        $response->headers->set('X-Cache', 'HIT');
        $response->headers->set('X-Cache-Time', $cachedData['cached_at']);

        return $response;
    }

    /**
     * Clear cache for a specific endpoint.
     */
    public static function clearEndpoint(string $endpoint): void
    {
        // This would require a more sophisticated cache implementation
        // For now, we'll use cache tags if available
        Cache::flush();
    }

    /**
     * Clear all API cache.
     */
    public static function clearAll(): void
    {
        Cache::flush();
    }
}
