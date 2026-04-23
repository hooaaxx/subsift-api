<?php

namespace Tests\Feature\Subscription;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_authenticated_user_can_list_their_subscriptions(): void
    {
        Subscription::factory()->count(3)->create(['user_id' => $this->user->id]);
        Subscription::factory()->count(2)->create();

        $response = $this->actingAs($this->user)->getJson('/api/v1/subscriptions');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data');
    }

    public function test_user_can_create_a_subscription(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/v1/subscriptions', [
            'name'              => 'Netflix',
            'cost'              => 15.99,
            'billing_cycle'     => 'monthly',
            'payment_method'    => 'credit_card',
            'next_billing_date' => '2026-05-01',
        ]);

        $response->assertStatus(201)
                 ->assertJsonPath('data.name', 'Netflix')
                 ->assertJsonPath('data.initials', 'NE');

        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $this->user->id,
            'name'    => 'Netflix',
        ]);
    }

    public function test_user_can_update_their_subscription(): void
    {
        $subscription = Subscription::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->putJson("/api/v1/subscriptions/{$subscription->id}", [
            'name'              => 'Updated Name',
            'cost'              => 19.99,
            'billing_cycle'     => 'yearly',
            'payment_method'    => 'app_store',
            'next_billing_date' => '2027-01-01',
        ]);

        $response->assertStatus(200)->assertJsonPath('data.name', 'Updated Name');
    }

    public function test_user_cannot_update_another_users_subscription(): void
    {
        $other = User::factory()->create();
        $subscription = Subscription::factory()->create(['user_id' => $other->id]);

        $response = $this->actingAs($this->user)->putJson("/api/v1/subscriptions/{$subscription->id}", [
            'name'              => 'Hacked',
            'cost'              => 0,
            'billing_cycle'     => 'monthly',
            'payment_method'    => 'credit_card',
            'next_billing_date' => '2026-05-01',
        ]);

        $response->assertStatus(403);
    }

    public function test_user_can_delete_their_subscription(): void
    {
        $subscription = Subscription::factory()->create(['user_id' => $this->user->id]);

        $this->actingAs($this->user)->deleteJson("/api/v1/subscriptions/{$subscription->id}")
             ->assertStatus(200);

        $this->assertDatabaseMissing('subscriptions', ['id' => $subscription->id]);
    }

    public function test_guest_cannot_access_subscriptions(): void
    {
        $this->getJson('/api/v1/subscriptions')->assertStatus(401);
    }
}
