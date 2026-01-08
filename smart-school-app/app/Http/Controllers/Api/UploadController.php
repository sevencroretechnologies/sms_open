<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * API Upload Controller
 * 
 * Handles file upload API endpoints.
 * This is a stub controller - full implementation pending.
 */
class UploadController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Upload endpoint - implementation pending',
            'data' => [
                'url' => '',
                'path' => '',
                'filename' => '',
            ],
        ]);
    }

    public function destroy($path): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => 'File deleted',
        ]);
    }
}
