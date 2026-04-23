<?php

namespace Tests\Feature\Subscription;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionSummaryTest extends TestCase
{
    use RefreshDatabase;

    public function test_summary_returns_monthly_and_annual_totals(): void
    {
        $user = User::factory()->create();

        Subscription::factory()->create(['user_id' => $user->id, 'cost' => 10.00, 'billing_cycle' => 'monthly']);
        Subscription::factory()->create(['user_id' => $user->id, 'cost' => 5.00, 'billing_cycle' => 'monthly']);
        Subscription::factory()->create(['user_id' => $user->id, 'cost' => 120.00, 'billing_cycle' => 'yearly']);

        $response = $this->actingAs($user)->getJson('/api/v1/subscriptions/summary');

        $response->assertStatus(200)
                 ->assertJsonPath('data.monthly_total', 15.00)
                 ->assertJsonPath('data.annual_total', 300.00);
    }
}
