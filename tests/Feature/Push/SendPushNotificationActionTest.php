<?php

namespace Tests\Feature\Push;

use App\Actions\SendPushNotificationAction;
use App\Models\PushSubscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SendPushNotificationActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_action_returns_early_when_no_subscriptions_exist(): void
    {
        $user = User::factory()->create();

        // Should not throw — user has no push subscriptions
        $action = new SendPushNotificationAction();
        $action->execute($user, 'Test Title', 'Test message');

        $this->assertDatabaseCount('push_subscriptions', 0);
    }

    public function test_action_returns_early_when_vapid_not_configured(): void
    {
        config(['services.vapid.public_key' => null]);

        $user = User::factory()->create();
        PushSubscription::factory()->create(['user_id' => $user->id]);

        // Should not throw even with a subscription — VAPID not configured
        $action = new SendPushNotificationAction();
        $action->execute($user, 'Test Title', 'Test message');

        $this->assertTrue(true);
    }
}
