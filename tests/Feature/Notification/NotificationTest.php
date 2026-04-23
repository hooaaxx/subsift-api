<?php

namespace Tests\Feature\Notification;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_user_can_list_their_notifications(): void
    {
        Notification::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'channel' => 'in_app',
        ]);

        $response = $this->actingAs($this->user)->getJson('/api/v1/notifications');

        $response->assertStatus(200)
                 ->assertJsonStructure(['success', 'data', 'meta']);
    }

    public function test_unread_count_returns_correct_number(): void
    {
        Notification::factory()->count(3)->create(['user_id' => $this->user->id, 'channel' => 'in_app', 'read_at' => null]);
        Notification::factory()->read()->create(['user_id' => $this->user->id, 'channel' => 'in_app']);

        $response = $this->actingAs($this->user)->getJson('/api/v1/notifications/unread-count');

        $response->assertStatus(200)->assertJsonPath('data.count', 3);
    }

    public function test_user_can_mark_notification_as_read(): void
    {
        $notification = Notification::factory()->create([
            'user_id' => $this->user->id,
            'channel' => 'in_app',
            'read_at' => null,
        ]);

        $response = $this->actingAs($this->user)
                         ->patchJson("/api/v1/notifications/{$notification->id}/read");

        $response->assertStatus(200);
        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_user_can_mark_all_notifications_as_read(): void
    {
        Notification::factory()->count(5)->create([
            'user_id' => $this->user->id,
            'channel' => 'in_app',
            'read_at' => null,
        ]);

        $this->actingAs($this->user)->patchJson('/api/v1/notifications/read-all')
             ->assertStatus(200);

        $unread = $this->user->notifications()->whereNull('read_at')->count();
        $this->assertEquals(0, $unread);
    }
}
