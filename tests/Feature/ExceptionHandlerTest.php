<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExceptionHandlerTest extends TestCase
{
    use RefreshDatabase;

    public function test_validation_error_returns_422_with_envelope(): void
    {
        $response = $this->postJson('/api/v1/auth/login', []);

        $response->assertStatus(422)
                 ->assertJsonStructure(['success', 'message', 'errors']);

        $this->assertFalse($response->json('success'));
    }

    public function test_unauthenticated_returns_401_with_envelope(): void
    {
        $response = $this->getJson('/api/v1/auth/me');

        $response->assertStatus(401)
                 ->assertJson(['success' => false]);
    }
}
