<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

/**
 * API Token Controller
 * 
 * Prompt 501: Create API Authentication Tokens
 * 
 * Manages API token generation and revocation using Laravel Sanctum.
 */
class TokenController extends Controller
{
    /**
     * Generate a new API token.
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required|string|max:255',
            'abilities' => 'array',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Define abilities based on user role
        $abilities = $request->abilities ?? $this->getDefaultAbilities($user);

        $token = $user->createToken(
            $request->device_name,
            $abilities
        );

        Log::info('API token created', [
            'user_id' => $user->id,
            'device_name' => $request->device_name,
            'abilities' => $abilities,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Token created successfully',
            'data' => [
                'token' => $token->plainTextToken,
                'token_type' => 'Bearer',
                'abilities' => $abilities,
                'expires_at' => null,
            ],
        ]);
    }

    /**
     * Get current user's tokens.
     */
    public function index(Request $request)
    {
        $tokens = $request->user()->tokens()->get()->map(function ($token) {
            return [
                'id' => $token->id,
                'name' => $token->name,
                'abilities' => $token->abilities,
                'last_used_at' => $token->last_used_at,
                'created_at' => $token->created_at,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $tokens,
        ]);
    }

    /**
     * Revoke a specific token.
     */
    public function destroy(Request $request, $tokenId)
    {
        $token = $request->user()->tokens()->find($tokenId);

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token not found',
            ], 404);
        }

        $token->delete();

        Log::info('API token revoked', [
            'user_id' => $request->user()->id,
            'token_id' => $tokenId,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Token revoked successfully',
        ]);
    }

    /**
     * Revoke all tokens for the current user.
     */
    public function destroyAll(Request $request)
    {
        $request->user()->tokens()->delete();

        Log::info('All API tokens revoked', [
            'user_id' => $request->user()->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'All tokens revoked successfully',
        ]);
    }

    /**
     * Revoke the current token.
     */
    public function revokeCurrent(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Current token revoked successfully',
        ]);
    }

    /**
     * Refresh the current token.
     */
    public function refresh(Request $request)
    {
        $user = $request->user();
        $currentToken = $user->currentAccessToken();
        
        // Create new token with same abilities
        $newToken = $user->createToken(
            $currentToken->name,
            $currentToken->abilities
        );

        // Delete old token
        $currentToken->delete();

        return response()->json([
            'success' => true,
            'message' => 'Token refreshed successfully',
            'data' => [
                'token' => $newToken->plainTextToken,
                'token_type' => 'Bearer',
            ],
        ]);
    }

    /**
     * Get default abilities based on user role.
     */
    protected function getDefaultAbilities(User $user): array
    {
        $abilities = ['read'];

        if ($user->hasRole('admin')) {
            $abilities = ['*'];
        } elseif ($user->hasRole('teacher')) {
            $abilities = ['read', 'write:attendance', 'write:marks', 'write:homework'];
        } elseif ($user->hasRole('student')) {
            $abilities = ['read:own', 'write:assignments'];
        } elseif ($user->hasRole('parent')) {
            $abilities = ['read:children'];
        } elseif ($user->hasRole('accountant')) {
            $abilities = ['read', 'write:fees', 'write:transactions'];
        } elseif ($user->hasRole('librarian')) {
            $abilities = ['read', 'write:library'];
        }

        return $abilities;
    }
}
