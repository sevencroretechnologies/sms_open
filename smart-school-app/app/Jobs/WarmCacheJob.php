<?php

namespace App\Jobs;

use App\Models\AcademicSession;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Warm Cache Job
 * 
 * Prompt 508: Create Cache Warming Jobs
 * 
 * Pre-populates cache with frequently accessed data to improve performance.
 */
class WarmCacheJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $cacheTypes;

    /**
     * Create a new job instance.
     */
    public function __construct(array $cacheTypes = ['all'])
    {
        $this->cacheTypes = $cacheTypes;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Starting cache warming', ['types' => $this->cacheTypes]);

        $startTime = microtime(true);

        if (in_array('all', $this->cacheTypes) || in_array('dashboard', $this->cacheTypes)) {
            $this->warmDashboardCache();
        }

        if (in_array('all', $this->cacheTypes) || in_array('academic', $this->cacheTypes)) {
            $this->warmAcademicCache();
        }

        if (in_array('all', $this->cacheTypes) || in_array('students', $this->cacheTypes)) {
            $this->warmStudentCache();
        }

        if (in_array('all', $this->cacheTypes) || in_array('settings', $this->cacheTypes)) {
            $this->warmSettingsCache();
        }

        $duration = round(microtime(true) - $startTime, 2);
        Log::info('Cache warming completed', ['duration_seconds' => $duration]);
    }

    /**
     * Warm dashboard cache.
     */
    protected function warmDashboardCache(): void
    {
        Log::info('Warming dashboard cache');

        // Cache total counts
        Cache::put('dashboard:total_students', Student::count(), 3600);
        Cache::put('dashboard:total_teachers', User::role('teacher')->count(), 3600);
        Cache::put('dashboard:total_classes', SchoolClass::count(), 3600);

        // Cache active session
        $activeSession = AcademicSession::where('is_active', true)->first();
        if ($activeSession) {
            Cache::put('dashboard:active_session', $activeSession, 3600);
        }
    }

    /**
     * Warm academic cache.
     */
    protected function warmAcademicCache(): void
    {
        Log::info('Warming academic cache');

        // Cache all classes with sections
        $classes = SchoolClass::with('sections')->get();
        Cache::put('academic:classes', $classes, 3600);

        // Cache academic sessions
        $sessions = AcademicSession::orderBy('start_date', 'desc')->get();
        Cache::put('academic:sessions', $sessions, 3600);
    }

    /**
     * Warm student cache.
     */
    protected function warmStudentCache(): void
    {
        Log::info('Warming student cache');

        // Cache student counts by class
        $classCounts = Student::selectRaw('class_id, COUNT(*) as count')
            ->groupBy('class_id')
            ->pluck('count', 'class_id')
            ->toArray();

        Cache::put('students:counts_by_class', $classCounts, 3600);
    }

    /**
     * Warm settings cache.
     */
    protected function warmSettingsCache(): void
    {
        Log::info('Warming settings cache');

        // Cache system settings
        $settings = \App\Models\Setting::pluck('value', 'key')->toArray();
        Cache::put('system:settings', $settings, 3600);
    }

    /**
     * Clear all warmed caches.
     */
    public static function clearAll(): void
    {
        $keys = [
            'dashboard:total_students',
            'dashboard:total_teachers',
            'dashboard:total_classes',
            'dashboard:active_session',
            'academic:classes',
            'academic:sessions',
            'students:counts_by_class',
            'system:settings',
        ];

        foreach ($keys as $key) {
            Cache::forget($key);
        }

        Log::info('All warmed caches cleared');
    }
}
