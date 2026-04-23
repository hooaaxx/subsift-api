<?php

namespace Tests\Unit\Models;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_subscription_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $subscription = Subscription::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $subscription->user);
        $this->assertEquals($user->id, $subscription->user->id);
    }

    public function test_cost_is_cast_to_float(): void
    {
        $subscription = Subscription::factory()->create(['cost' => '9.99']);
        $this->assertIsFloat($subscription->fresh()->cost);
    }

    public function test_is_trial_is_cast_to_bool(): void
    {
        $subscription = Subscription::factory()->create(['is_trial' => 1]);
        $this->assertIsBool($subscription->fresh()->is_trial);
    }

    public function test_next_billing_date_is_cast_to_date(): void
    {
        $subscription = Subscription::factory()->create([
            'next_billing_date' => '2026-05-01',
        ]);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $subscription->fresh()->next_billing_date);
    }
}
