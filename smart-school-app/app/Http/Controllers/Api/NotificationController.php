<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * API Notification Controller
 * 
 * Handles notification API endpoints.
 * This is a stub controller - full implementation pending.
 */
class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => [],
            'meta' => ['unread_count' => 0],
        ]);
    }

    public function markAsRead($notification): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Notification marked as read',
        ]);
    }

    public function markAllAsRead(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => 'All notifications marked as read',
        ]);
    }

    public function unreadCount(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => ['count' => 0],
        ]);
    }
}
