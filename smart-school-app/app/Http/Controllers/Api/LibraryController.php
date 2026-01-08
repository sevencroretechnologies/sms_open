<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * API Library Controller
 * 
 * Handles library API endpoints.
 * This is a stub controller - full implementation pending.
 */
class LibraryController extends Controller
{
    public function books(Request $request): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => [],
            'meta' => ['current_page' => 1, 'per_page' => 15, 'total' => 0],
        ]);
    }

    public function book($book): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => null,
        ]);
    }

    public function members(Request $request): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => [],
        ]);
    }

    public function issues(Request $request): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => [],
        ]);
    }

    public function overdue(Request $request): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => [],
        ]);
    }
}
