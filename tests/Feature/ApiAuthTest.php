<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_login_returns_user_resource_and_token(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $user = User::factory()->create([
            'email' => 'api-user@example.com',
            'password' => 'password',
        ]);
        $user->assignRole('user');

        $this->postJson('/api/v1/login', [
            'email' => 'api-user@example.com',
            'password' => 'password',
        ])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.user.email', 'api-user@example.com')
            ->assertJsonPath('data.user.status', 'active')
            ->assertJsonStructure([
                'data' => [
                    'user' => ['id', 'name', 'email', 'status', 'role_type'],
                    'token',
                ],
            ]);
    }

    public function test_api_me_requires_authentication(): void
    {
        $this->getJson('/api/v1/me')
            ->assertUnauthorized()
            ->assertJsonPath('success', false);
    }

    public function test_api_me_returns_authenticated_user_resource(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $user = User::factory()->create([
            'email' => 'me@example.com',
        ]);
        $user->assignRole('user');
        $token = $user->createToken('test-token')->plainTextToken;

        $this->withToken($token)
            ->getJson('/api/v1/me')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.email', 'me@example.com')
            ->assertJsonPath('data.roles.0', 'user');
    }
}
