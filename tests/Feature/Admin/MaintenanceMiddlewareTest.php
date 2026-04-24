<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class MaintenanceMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Cache::forget('maintenance_mode');
        parent::tearDown();
    }

    public function test_regular_user_gets_503_during_maintenance(): void
    {
        Cache::put('maintenance_mode', true);
        $user = User::factory()->create();

        $this->actingAs($user)
             ->getJson('/api/v1/subscriptions')
             ->assertStatus(503)
             ->assertJsonPath('success', false)
             ->assertJsonPath('message', 'Under maintenance.');
    }

    public function test_admin_user_bypasses_maintenance(): void
    {
        Cache::put('maintenance_mode', true);
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
             ->getJson('/api/v1/auth/me')
             ->assertStatus(200);
    }

    public function test_regular_user_can_access_app_when_maintenance_is_off(): void
    {
        Cache::put('maintenance_mode', false);
        $user = User::factory()->create();

        $this->actingAs($user)
             ->getJson('/api/v1/subscriptions')
             ->assertStatus(200);
    }

    public function test_regular_user_can_still_call_me_during_maintenance(): void
    {
        Cache::put('maintenance_mode', true);
        $user = User::factory()->create();

        $this->actingAs($user)
             ->getJson('/api/v1/auth/me')
             ->assertStatus(200);
    }
}
