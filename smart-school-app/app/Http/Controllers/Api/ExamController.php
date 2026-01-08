<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * API Exam Controller
 * 
 * Handles exam-related API endpoints for AJAX requests.
 * This is a stub controller - full implementation pending.
 */
class ExamController extends Controller
{
    /**
     * List exams with pagination and filters.
     */
    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Exams endpoint - implementation pending',
            'data' => [],
            'meta' => [
                'current_page' => 1,
                'per_page' => 15,
                'total' => 0,
            ],
        ]);
    }

    /**
     * Get exam details.
     */
    public function show($exam): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Exam details endpoint - implementation pending',
            'data' => null,
        ]);
    }

    /**
     * Get exam schedules.
     */
    public function schedules($exam): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Exam schedules endpoint - implementation pending',
            'data' => [],
        ]);
    }

    /**
     * Get exam marks.
     */
    public function marks($exam): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Exam marks endpoint - implementation pending',
            'data' => [],
        ]);
    }

    /**
     * Save exam marks.
     */
    public function saveMarks(Request $request, $exam): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Save marks endpoint - implementation pending',
        ]);
    }

    /**
     * Get exam results.
     */
    public function results($exam): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Exam results endpoint - implementation pending',
            'data' => [],
        ]);
    }
}
