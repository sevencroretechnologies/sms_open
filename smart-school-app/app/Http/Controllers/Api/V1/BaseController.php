<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

/**
 * Base API Controller for V1
 * 
 * Prompt 500: Implement API Versioning Strategy
 * 
 * Provides common functionality for all V1 API controllers.
 */
class BaseController extends Controller
{
    /**
     * API version
     */
    protected string $version = 'v1';

    /**
     * Send success response.
     */
    protected function sendResponse($data, string $message = 'Success', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'api_version' => $this->version,
        ], $code);
    }

    /**
     * Send error response.
     */
    protected function sendError(string $message, array $errors = [], int $code = 400): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
            'api_version' => $this->version,
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    /**
     * Send paginated response.
     */
    protected function sendPaginatedResponse($paginator, string $message = 'Success'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $paginator->items(),
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
            'api_version' => $this->version,
        ]);
    }

    /**
     * Send created response.
     */
    protected function sendCreated($data, string $message = 'Created successfully'): JsonResponse
    {
        return $this->sendResponse($data, $message, 201);
    }

    /**
     * Send no content response.
     */
    protected function sendNoContent(): JsonResponse
    {
        return response()->json(null, 204);
    }

    /**
     * Send unauthorized response.
     */
    protected function sendUnauthorized(string $message = 'Unauthorized'): JsonResponse
    {
        return $this->sendError($message, [], 401);
    }

    /**
     * Send forbidden response.
     */
    protected function sendForbidden(string $message = 'Forbidden'): JsonResponse
    {
        return $this->sendError($message, [], 403);
    }

    /**
     * Send not found response.
     */
    protected function sendNotFound(string $message = 'Resource not found'): JsonResponse
    {
        return $this->sendError($message, [], 404);
    }

    /**
     * Send validation error response.
     */
    protected function sendValidationError(array $errors, string $message = 'Validation failed'): JsonResponse
    {
        return $this->sendError($message, $errors, 422);
    }

    /**
     * Send server error response.
     */
    protected function sendServerError(string $message = 'Internal server error'): JsonResponse
    {
        return $this->sendError($message, [], 500);
    }
}
