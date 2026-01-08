<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * API Transport Controller
 * 
 * Handles transport API endpoints.
 * This is a stub controller - full implementation pending.
 */
class TransportController extends Controller
{
    public function routes(Request $request): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => [],
        ]);
    }

    public function route($route): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => null,
        ]);
    }

    public function vehicles(Request $request): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => [],
        ]);
    }

    public function students(Request $request): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => [],
        ]);
    }
}
