<?php

namespace Tests\Unit\Models;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_notification_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $notification = Notification::factory()->create(['user_id' => $user->id]);

        $this->assertEquals($user->id, $notification->user->id);
    }

    public function test_is_unread_when_read_at_is_null(): void
    {
        $notification = Notification::factory()->create(['read_at' => null]);
        $this->assertTrue($notification->isUnread());
    }

    public function test_is_not_unread_when_read_at_is_set(): void
    {
        $notification = Notification::factory()->create(['read_at' => now()]);
        $this->assertFalse($notification->isUnread());
    }
}
