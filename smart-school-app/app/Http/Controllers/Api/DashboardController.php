<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * API Dashboard Controller
 * 
 * Handles dashboard metrics and chart data API endpoints.
 * This is a stub controller - full implementation pending.
 */
class DashboardController extends Controller
{
    public function metrics(Request $request): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'total_students' => 0,
                'total_teachers' => 0,
                'total_classes' => 0,
                'fees_collected' => 0,
            ],
        ]);
    }

    public function attendanceChart(Request $request): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => ['labels' => [], 'datasets' => []],
        ]);
    }

    public function feesChart(Request $request): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => ['labels' => [], 'datasets' => []],
        ]);
    }

    public function studentsChart(Request $request): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => ['labels' => [], 'datasets' => []],
        ]);
    }

    public function recentActivities(Request $request): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => [],
        ]);
    }
}
