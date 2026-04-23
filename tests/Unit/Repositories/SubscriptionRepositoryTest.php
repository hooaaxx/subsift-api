<?php

namespace Tests\Unit\Repositories;

use App\Models\Subscription;
use App\Models\User;
use App\Repositories\SubscriptionRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private SubscriptionRepository $repo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repo = new SubscriptionRepository();
    }

    public function test_all_for_user_returns_only_that_users_subscriptions(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        Subscription::factory()->count(3)->create(['user_id' => $user->id]);
        Subscription::factory()->count(2)->create(['user_id' => $other->id]);

        $result = $this->repo->allForUser($user);

        $this->assertCount(3, $result);
        $result->each(fn ($s) => $this->assertEquals($user->id, $s->user_id));
    }

    public function test_due_for_alert_returns_subscriptions_within_48_to_72_hours(): void
    {
        $user = User::factory()->create();

        $inWindow = Subscription::factory()->create([
            'user_id'           => $user->id,
            'alert_enabled'     => true,
            'next_billing_date' => now()->addHours(60)->toDateString(),
        ]);

        Subscription::factory()->create([
            'user_id'           => $user->id,
            'alert_enabled'     => true,
            'next_billing_date' => now()->addHours(10)->toDateString(),
        ]);

        Subscription::factory()->create([
            'user_id'           => $user->id,
            'alert_enabled'     => false,
            'next_billing_date' => now()->addHours(60)->toDateString(),
        ]);

        $result = $this->repo->dueForAlert();

        $this->assertCount(1, $result);
        $this->assertEquals($inWindow->id, $result->first()->id);
    }
}
