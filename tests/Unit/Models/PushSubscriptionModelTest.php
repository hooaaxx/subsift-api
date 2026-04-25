<?php

namespace Tests\Unit\Models;

use App\Models\PushSubscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PushSubscriptionModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $sub  = PushSubscription::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($sub->user->is($user));
    }

    public function test_deleting_user_cascades_to_push_subscriptions(): void
    {
        $user = User::factory()->create();
        PushSubscription::factory()->create(['user_id' => $user->id]);

        $user->delete();

        $this->assertDatabaseCount('push_subscriptions', 0);
    }
}
