<?php

namespace Tests\Feature;

use App\Models\OrganizationSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_returns_mock_data()
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'mockuser@nme.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['message', 'token', 'user']);
    }

    public function test_register_creates_user_and_returns_token()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'testuser@nme.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'user_type' => 'buyer',
        ];

        $response = $this->postJson('/api/auth/register', $userData);

        $response->assertStatus(201)
            ->assertJsonStructure(['message', 'token', 'user']);

        $this->assertDatabaseHas('users', ['email' => 'testuser@nme.com']);
    }

    public function test_forgot_password_requires_email()
    {
        $response = $this->postJson('/api/auth/forgot-password', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }

    public function test_forgot_password_accepts_valid_email()
    {
        $user = User::factory()->create([
            'email' => 'forgot@nme.com',
        ]);

        $response = $this->postJson('/api/auth/forgot-password', [
            'email' => 'forgot@nme.com',
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'If your email exists in our system, we have sent a password reset link.']);
    }

    public function test_registration_blocked_when_disabled()
    {
        // Disable registration
        OrganizationSetting::updateOrCreate(
            ['key' => 'registration_enabled'],
            ['value' => 'false', 'type' => 'boolean']
        );

        $userData = [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'testuser@nme.com',
            'phone' => '+2348000000000',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'user_type' => 'buyer',
            'company_name' => 'Test Company',
        ];

        $response = $this->postJson('/api/auth/register', $userData);

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'The user registration feature is currently disabled',
                'code' => 'FEATURE_DISABLED'
            ]);
    }

    public function test_registration_allowed_when_enabled()
    {
        // Ensure registration is enabled
        OrganizationSetting::updateOrCreate(
            ['key' => 'registration_enabled'],
            ['value' => 'true', 'type' => 'boolean']
        );

        $userData = [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'testuser@nme.com',
            'phone' => '+2348000000000',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'user_type' => 'buyer',
            'company_name' => 'Test Company',
        ];

        $response = $this->postJson('/api/auth/register', $userData);

        $response->assertStatus(201)
            ->assertJsonStructure(['message', 'token', 'user']);
    }
}
