<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password123')]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email'    => $user->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['success', 'data' => [
                     'user' => ['id', 'name', 'email'],
                     'access_token',
                     'token_type'
                 ]])
                 ->assertJsonPath('success', true);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password123')]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email'    => $user->email,
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(422)->assertJsonPath('success', false);
    }

    public function test_authenticated_user_can_fetch_profile(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/v1/auth/me');

        $response->assertStatus(200)
                 ->assertJsonPath('data.email', $user->email);
    }

    public function test_unauthenticated_user_cannot_fetch_profile(): void
    {
        $this->getJson('/api/v1/auth/me')->assertStatus(401);
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->postJson('/api/v1/auth/logout');

        $response->assertStatus(200)
                 ->assertJsonPath('success', true);

        $this->assertCount(0, $user->tokens);
    }
}
