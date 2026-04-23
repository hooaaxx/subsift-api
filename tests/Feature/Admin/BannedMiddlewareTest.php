<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BannedMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_banned_user_cannot_access_protected_routes(): void
    {
        $user = User::factory()->create(['banned_at' => now()]);

        $this->actingAs($user)
             ->getJson('/api/v1/auth/me')
             ->assertStatus(403)
             ->assertJsonPath('message', 'Your account has been suspended.');
    }

    public function test_active_user_can_access_protected_routes(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
             ->getJson('/api/v1/auth/me')
             ->assertStatus(200);
    }
}
