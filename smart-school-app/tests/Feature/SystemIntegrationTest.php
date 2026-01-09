<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\AcademicSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * System Integration Test
 * 
 * Prompt 512: Final System Integration Test
 * 
 * Comprehensive integration tests for the entire system.
 */
class SystemIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $teacherUser;
    protected User $studentUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create users with different roles
        $this->adminUser = User::factory()->create(['email' => 'admin@test.com']);
        $this->adminUser->assignRole('admin');

        $this->teacherUser = User::factory()->create(['email' => 'teacher@test.com']);
        $this->teacherUser->assignRole('teacher');

        $this->studentUser = User::factory()->create(['email' => 'student@test.com']);
        $this->studentUser->assignRole('student');
    }

    /**
     * Test admin can access dashboard.
     */
    public function test_admin_can_access_dashboard(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/admin/dashboard');

        $response->assertStatus(200);
    }

    /**
     * Test teacher can access dashboard.
     */
    public function test_teacher_can_access_dashboard(): void
    {
        $response = $this->actingAs($this->teacherUser)->get('/teacher/dashboard');

        $response->assertStatus(200);
    }

    /**
     * Test student can access dashboard.
     */
    public function test_student_can_access_dashboard(): void
    {
        $response = $this->actingAs($this->studentUser)->get('/student/dashboard');

        $response->assertStatus(200);
    }

    /**
     * Test unauthenticated user is redirected to login.
     */
    public function test_unauthenticated_user_redirected_to_login(): void
    {
        $response = $this->get('/admin/dashboard');

        $response->assertRedirect('/login');
    }

    /**
     * Test login functionality.
     */
    public function test_user_can_login(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@test.com',
            'password' => 'password',
        ]);

        $response->assertRedirect();
        $this->assertAuthenticated();
    }

    /**
     * Test logout functionality.
     */
    public function test_user_can_logout(): void
    {
        $response = $this->actingAs($this->adminUser)->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    /**
     * Test locale switching.
     */
    public function test_locale_switching(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->post('/locale', ['locale' => 'ar']);

        $response->assertRedirect();
        $this->assertEquals('ar', session('locale'));
    }

    /**
     * Test RTL layout for Arabic locale.
     */
    public function test_rtl_layout_for_arabic(): void
    {
        session(['locale' => 'ar']);

        $response = $this->actingAs($this->adminUser)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('dir="rtl"', false);
    }

    /**
     * Test API health endpoint.
     */
    public function test_api_health_endpoint(): void
    {
        $response = $this->getJson('/api/health');

        $response->assertStatus(200)
            ->assertJson(['status' => 'ok']);
    }

    /**
     * Test security headers are present.
     */
    public function test_security_headers_present(): void
    {
        $response = $this->get('/');

        $response->assertHeader('X-Content-Type-Options');
        $response->assertHeader('X-Frame-Options');
    }

    /**
     * Test CSRF protection.
     */
    public function test_csrf_protection(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@test.com',
            'password' => 'password',
        ]);

        // Without CSRF token, should fail
        $response->assertStatus(419);
    }

    /**
     * Test profile update.
     */
    public function test_user_can_update_profile(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->patch('/profile', [
                'name' => 'Updated Name',
                'email' => 'admin@test.com',
            ]);

        $response->assertRedirect();
        $this->assertEquals('Updated Name', $this->adminUser->fresh()->name);
    }

    /**
     * Test error handling.
     */
    public function test_404_error_handling(): void
    {
        $response = $this->get('/non-existent-page');

        $response->assertStatus(404);
    }

    /**
     * Test database connectivity.
     */
    public function test_database_connectivity(): void
    {
        $this->assertDatabaseHas('users', [
            'email' => 'admin@test.com',
        ]);
    }

    /**
     * Test cache functionality.
     */
    public function test_cache_functionality(): void
    {
        cache()->put('test_key', 'test_value', 60);

        $this->assertEquals('test_value', cache()->get('test_key'));

        cache()->forget('test_key');

        $this->assertNull(cache()->get('test_key'));
    }

    /**
     * Test session functionality.
     */
    public function test_session_functionality(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->withSession(['test_key' => 'test_value'])
            ->get('/admin/dashboard');

        $response->assertSessionHas('test_key', 'test_value');
    }
}
