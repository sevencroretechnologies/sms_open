<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

/**
 * Optimized Queries Trait
 * 
 * Prompt 507: Add Database Query Optimization
 * 
 * Provides query optimization methods for Eloquent models.
 */
trait OptimizedQueries
{
    /**
     * Scope to select only specific columns.
     */
    public function scopeSelectOnly(Builder $query, array $columns): Builder
    {
        return $query->select($columns);
    }

    /**
     * Scope to eager load relationships efficiently.
     */
    public function scopeWithOptimized(Builder $query, array $relations): Builder
    {
        foreach ($relations as $relation => $columns) {
            if (is_numeric($relation)) {
                $query->with($columns);
            } else {
                $query->with([$relation => function ($q) use ($columns) {
                    $q->select($columns);
                }]);
            }
        }

        return $query;
    }

    /**
     * Scope to cache query results.
     */
    public function scopeCached(Builder $query, int $ttl = 300, ?string $key = null): Builder
    {
        $cacheKey = $key ?? $this->generateCacheKey($query);

        return Cache::remember($cacheKey, $ttl, function () use ($query) {
            return $query->get();
        });
    }

    /**
     * Generate cache key from query.
     */
    protected function generateCacheKey(Builder $query): string
    {
        return 'query:' . md5($query->toSql() . serialize($query->getBindings()));
    }

    /**
     * Scope for efficient pagination.
     */
    public function scopeEfficientPaginate(Builder $query, int $perPage = 15): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $query->paginate($perPage, ['*'], 'page', request()->get('page', 1));
    }

    /**
     * Scope to chunk results for processing.
     */
    public function scopeChunkProcess(Builder $query, int $count, callable $callback): bool
    {
        return $query->chunk($count, $callback);
    }

    /**
     * Scope for cursor-based pagination (more efficient for large datasets).
     */
    public function scopeCursorPaginate(Builder $query, int $perPage = 15): \Illuminate\Contracts\Pagination\CursorPaginator
    {
        return $query->cursorPaginate($perPage);
    }

    /**
     * Scope to add index hints.
     */
    public function scopeUseIndex(Builder $query, string $index): Builder
    {
        return $query->from(\DB::raw("{$this->getTable()} USE INDEX ({$index})"));
    }

    /**
     * Scope for efficient count.
     */
    public function scopeEfficientCount(Builder $query): int
    {
        return $query->toBase()->getCountForPagination();
    }

    /**
     * Scope to avoid N+1 queries.
     */
    public function scopePreventNPlusOne(Builder $query, array $relations): Builder
    {
        return $query->with($relations);
    }

    /**
     * Scope for batch insert.
     */
    public static function batchInsert(array $records, int $batchSize = 1000): bool
    {
        $chunks = array_chunk($records, $batchSize);

        foreach ($chunks as $chunk) {
            static::insert($chunk);
        }

        return true;
    }

    /**
     * Scope for batch update.
     */
    public static function batchUpdate(array $records, string $keyColumn = 'id'): int
    {
        $updated = 0;

        foreach ($records as $record) {
            $key = $record[$keyColumn];
            unset($record[$keyColumn]);

            $updated += static::where($keyColumn, $key)->update($record);
        }

        return $updated;
    }

    /**
     * Clear model cache.
     */
    public static function clearModelCache(): void
    {
        $table = (new static)->getTable();
        Cache::forget("model_cache:{$table}");
    }
}
