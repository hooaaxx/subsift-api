<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class AdminControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    protected function tearDown(): void
    {
        Cache::forget('maintenance_mode');
        parent::tearDown();
    }

    // ── Access control ────────────────────────────────────────────────────────

    public function test_regular_user_cannot_access_admin_routes(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
             ->getJson('/api/v1/admin/users')
             ->assertStatus(403);
    }

    public function test_unauthenticated_request_cannot_access_admin_routes(): void
    {
        $this->getJson('/api/v1/admin/users')->assertStatus(401);
    }

    // ── Users list ────────────────────────────────────────────────────────────

    public function test_admin_can_list_users(): void
    {
        User::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)
                         ->getJson('/api/v1/admin/users');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data')
                 ->assertJsonStructure(['data' => [['id', 'name', 'email', 'role', 'banned_at', 'created_at']]]);
    }

    public function test_admin_is_excluded_from_user_list(): void
    {
        User::factory()->count(2)->create();

        $response = $this->actingAs($this->admin)
                         ->getJson('/api/v1/admin/users');

        $response->assertStatus(200)->assertJsonCount(2, 'data');

        $ids = collect($response->json('data'))->pluck('id');
        $this->assertNotContains($this->admin->id, $ids);
    }

    // ── Ban ───────────────────────────────────────────────────────────────────

    public function test_admin_can_ban_a_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->admin)
                         ->postJson("/api/v1/admin/users/{$user->id}/ban");

        $response->assertStatus(200)
                 ->assertJsonPath('data.banned_at', fn ($v) => $v !== null);

        $this->assertNotNull($user->fresh()->banned_at);
    }

    public function test_ban_deletes_user_tokens(): void
    {
        $user  = User::factory()->create();
        $user->createToken('test');

        $this->assertCount(1, $user->tokens);

        $this->actingAs($this->admin)
             ->postJson("/api/v1/admin/users/{$user->id}/ban")
             ->assertStatus(200);

        $this->assertCount(0, $user->fresh()->tokens);
    }

    public function test_admin_cannot_ban_themselves(): void
    {
        $this->actingAs($this->admin)
             ->postJson("/api/v1/admin/users/{$this->admin->id}/ban")
             ->assertStatus(403);
    }

    public function test_ban_returns_404_for_nonexistent_user(): void
    {
        $this->actingAs($this->admin)
             ->postJson('/api/v1/admin/users/99999/ban')
             ->assertStatus(404);
    }

    // ── Unban ─────────────────────────────────────────────────────────────────

    public function test_admin_can_unban_a_user(): void
    {
        $user = User::factory()->create(['banned_at' => now()]);

        $response = $this->actingAs($this->admin)
                         ->postJson("/api/v1/admin/users/{$user->id}/unban");

        $response->assertStatus(200)
                 ->assertJsonPath('data.banned_at', null);

        $this->assertNull($user->fresh()->banned_at);
    }

    // ── Maintenance ───────────────────────────────────────────────────────────

    public function test_admin_can_get_maintenance_status(): void
    {
        Cache::put('maintenance_mode', true);

        $this->actingAs($this->admin)
             ->getJson('/api/v1/admin/maintenance')
             ->assertStatus(200)
             ->assertJsonPath('data.enabled', true);
    }

    public function test_admin_can_enable_maintenance_mode(): void
    {
        $this->actingAs($this->admin)
             ->postJson('/api/v1/admin/maintenance', ['enabled' => true])
             ->assertStatus(200)
             ->assertJsonPath('data.enabled', true);

        $this->assertTrue(Cache::get('maintenance_mode'));
    }

    public function test_admin_can_disable_maintenance_mode(): void
    {
        Cache::put('maintenance_mode', true);

        $this->actingAs($this->admin)
             ->postJson('/api/v1/admin/maintenance', ['enabled' => false])
             ->assertStatus(200)
             ->assertJsonPath('data.enabled', false);

        $this->assertFalse(Cache::get('maintenance_mode'));
    }

    // ── Public status ─────────────────────────────────────────────────────────

    public function test_public_status_endpoint_returns_maintenance_flag(): void
    {
        Cache::put('maintenance_mode', true);

        $this->getJson('/api/v1/status')
             ->assertStatus(200)
             ->assertJsonPath('data.maintenance', true);
    }
}
