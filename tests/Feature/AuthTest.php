<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    public function test_login_with_valid_credentials()
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'admin@petrotekno.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user' => [
                        'id',
                        'email',
                        'rol',
                        'permisos',
                    ],
                    'token',
                ],
            ]);
    }

    public function test_login_with_invalid_credentials()
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'admin@petrotekno.com',
            'password' => 'wrong_password',
        ]);

        $response->assertStatus(422);
    }

    public function test_authenticated_user_can_access_protected_routes()
    {
        $user = User::where('email', 'admin@petrotekno.com')->first();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/auth/me');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'email',
                    'rol',
                    'permisos',
                ],
            ]);
    }

    public function test_user_can_logout()
    {
        $user = User::where('email', 'admin@petrotekno.com')->first();
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson('/api/auth/logout');

        $response->assertStatus(200);
    }
}
