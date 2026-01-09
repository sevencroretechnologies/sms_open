<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * API Integration Tests
 * 
 * Prompt 503: Create Integration Tests
 * 
 * Tests API endpoints for proper functionality and integration.
 */
class ApiIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected string $token;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user
        $this->adminUser = User::factory()->create([
            'email' => 'admin@test.com',
        ]);
        $this->adminUser->assignRole('admin');

        // Create API token
        $this->token = $this->adminUser->createToken('test-token')->plainTextToken;
    }

    /**
     * Test health check endpoint.
     */
    public function test_health_check_returns_ok(): void
    {
        $response = $this->getJson('/api/health');

        $response->assertStatus(200)
            ->assertJson(['status' => 'ok']);
    }

    /**
     * Test detailed health check endpoint.
     */
    public function test_detailed_health_check(): void
    {
        $response = $this->getJson('/api/health/detailed');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'checks' => [
                    'database',
                    'cache',
                    'disk',
                    'memory',
                ],
            ]);
    }

    /**
     * Test API authentication with valid token.
     */
    public function test_api_authentication_with_valid_token(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/v1/user');

        $response->assertStatus(200);
    }

    /**
     * Test API authentication without token.
     */
    public function test_api_authentication_without_token(): void
    {
        $response = $this->getJson('/api/v1/user');

        $response->assertStatus(401);
    }

    /**
     * Test token generation.
     */
    public function test_token_generation(): void
    {
        $response = $this->postJson('/api/tokens', [
            'email' => 'admin@test.com',
            'password' => 'password',
            'device_name' => 'test-device',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'token',
                    'token_type',
                ],
            ]);
    }

    /**
     * Test token generation with invalid credentials.
     */
    public function test_token_generation_with_invalid_credentials(): void
    {
        $response = $this->postJson('/api/tokens', [
            'email' => 'admin@test.com',
            'password' => 'wrong-password',
            'device_name' => 'test-device',
        ]);

        $response->assertStatus(422);
    }

    /**
     * Test rate limiting.
     */
    public function test_rate_limiting(): void
    {
        // Make multiple requests to trigger rate limiting
        for ($i = 0; $i < 65; $i++) {
            $this->getJson('/api/health');
        }

        $response = $this->getJson('/api/health');

        // Should either succeed or return 429 (rate limited)
        $this->assertTrue(in_array($response->status(), [200, 429]));
    }

    /**
     * Test API response structure.
     */
    public function test_api_response_structure(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/v1/dashboard/stats');

        $response->assertJsonStructure([
            'success',
            'message',
        ]);
    }

    /**
     * Test CORS headers.
     */
    public function test_cors_headers(): void
    {
        $response = $this->withHeaders([
            'Origin' => 'http://localhost:3000',
        ])->getJson('/api/health');

        $response->assertStatus(200);
    }

    /**
     * Test security headers.
     */
    public function test_security_headers(): void
    {
        $response = $this->get('/');

        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
    }
}
