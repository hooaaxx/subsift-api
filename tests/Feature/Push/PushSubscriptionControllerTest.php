<?php

namespace Tests\Feature\Push;

use App\Models\PushSubscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PushSubscriptionControllerTest extends TestCase
{
    use RefreshDatabase;

    private array $payload = [
        'endpoint'   => 'https://fcm.googleapis.com/fcm/send/abc123',
        'public_key' => 'BNcRdreALRFXTkOOUHK7',
        'auth_token' => 'tBHItJI5svbpez7KI4CCXg',
    ];

    public function test_authenticated_user_can_subscribe(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/v1/push/subscribe', $this->payload)
             ->assertStatus(201)
             ->assertJson(['success' => true]);

        $this->assertDatabaseHas('push_subscriptions', [
            'endpoint' => $this->payload['endpoint'],
        ]);
    }

    public function test_subscribing_same_endpoint_twice_upserts(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->postJson('/api/v1/push/subscribe', $this->payload)->assertStatus(201);
        $this->postJson('/api/v1/push/subscribe', $this->payload)->assertStatus(200);

        $this->assertDatabaseCount('push_subscriptions', 1);
    }

    public function test_unauthenticated_user_cannot_subscribe(): void
    {
        $this->postJson('/api/v1/push/subscribe', $this->payload)->assertStatus(401);
    }

    public function test_subscribe_requires_endpoint_public_key_and_auth_token(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/v1/push/subscribe', [])->assertStatus(422);
    }

    public function test_authenticated_user_can_unsubscribe(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        PushSubscription::factory()->create([
            'user_id'  => $user->id,
            'endpoint' => $this->payload['endpoint'],
        ]);

        $this->deleteJson('/api/v1/push/unsubscribe', ['endpoint' => $this->payload['endpoint']])
             ->assertStatus(200)
             ->assertJson(['success' => true]);

        $this->assertDatabaseCount('push_subscriptions', 0);
    }

    public function test_unsubscribe_only_deletes_own_subscription(): void
    {
        $user  = User::factory()->create();
        $other = User::factory()->create();

        PushSubscription::factory()->create([
            'user_id'  => $other->id,
            'endpoint' => $this->payload['endpoint'],
        ]);

        Sanctum::actingAs($user);

        $this->deleteJson('/api/v1/push/unsubscribe', ['endpoint' => $this->payload['endpoint']])
             ->assertStatus(200);

        $this->assertDatabaseCount('push_subscriptions', 1);
    }
}
