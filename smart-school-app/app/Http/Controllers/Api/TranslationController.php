<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * API Translation Controller
 * 
 * Handles translation API endpoints.
 * This is a stub controller - full implementation pending.
 */
class TranslationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $group = $request->get('group', 'nav');
        
        return response()->json([
            'status' => 'success',
            'data' => [],
            'locale' => app()->getLocale(),
        ]);
    }
}
