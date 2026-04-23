<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_with_valid_data(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name'                  => 'Michel Bonganay',
            'email'                 => 'michel@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure(['success', 'data' => [
                     'user' => ['id', 'name', 'email'],
                     'access_token',
                     'token_type'
                 ], 'message']);

        $this->assertTrue($response->json('success'));
        $this->assertDatabaseHas('users', ['email' => 'michel@example.com']);
    }

    public function test_register_fails_with_duplicate_email(): void
    {
        User::factory()->create(['email' => 'michel@example.com']);

        $response = $this->postJson('/api/v1/auth/register', [
            'name'                  => 'Michel Bonganay',
            'email'                 => 'michel@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422)->assertJsonPath('success', false);
    }

    public function test_register_fails_with_missing_fields(): void
    {
        $response = $this->postJson('/api/v1/auth/register', []);
        $response->assertStatus(422)->assertJsonStructure(['errors']);
    }
}
