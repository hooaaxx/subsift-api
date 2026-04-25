<?php

namespace Tests\Feature\Notification;

use App\Jobs\SendRenewalVerificationJob;
use App\Models\RenewalVerificationToken;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class RenewalVerificationCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_dispatches_job_for_subscription_due_yesterday(): void
    {
        Queue::fake();
        $user = User::factory()->create();
        Subscription::factory()->create([
            'user_id'           => $user->id,
            'next_billing_date' => Carbon::yesterday()->toDateString(),
            'is_trial'          => false,
            'status'            => 'active',
        ]);

        $this->artisan('subsift:check-renewal-verifications')->assertExitCode(0);

        Queue::assertPushed(SendRenewalVerificationJob::class, 1);
    }

    public function test_command_skips_trial_subscriptions(): void
    {
        Queue::fake();
        $user = User::factory()->create();
        Subscription::factory()->create([
            'user_id'           => $user->id,
            'next_billing_date' => Carbon::yesterday()->toDateString(),
            'is_trial'          => true,
        ]);

        $this->artisan('subsift:check-renewal-verifications')->assertExitCode(0);

        Queue::assertNotPushed(SendRenewalVerificationJob::class);
    }

    public function test_command_skips_cancelled_subscriptions(): void
    {
        Queue::fake();
        $user = User::factory()->create();
        Subscription::factory()->create([
            'user_id'           => $user->id,
            'next_billing_date' => Carbon::yesterday()->toDateString(),
            'is_trial'          => false,
            'status'            => 'cancelled',
        ]);

        $this->artisan('subsift:check-renewal-verifications')->assertExitCode(0);

        Queue::assertNotPushed(SendRenewalVerificationJob::class);
    }

    public function test_command_skips_subscriptions_with_existing_pending_tokens(): void
    {
        Queue::fake();
        $user = User::factory()->create();
        $sub  = Subscription::factory()->create([
            'user_id'           => $user->id,
            'next_billing_date' => Carbon::yesterday()->toDateString(),
            'is_trial'          => false,
            'status'            => 'active',
        ]);

        RenewalVerificationToken::factory()->create([
            'subscription_id' => $sub->id,
            'used_at'         => null,
            'expires_at'      => now()->addDays(5),
        ]);

        $this->artisan('subsift:check-renewal-verifications')->assertExitCode(0);

        Queue::assertNotPushed(SendRenewalVerificationJob::class);
    }

    public function test_command_skips_subscriptions_not_due_yesterday(): void
    {
        Queue::fake();
        $user = User::factory()->create();
        Subscription::factory()->create([
            'user_id'           => $user->id,
            'next_billing_date' => Carbon::today()->toDateString(),
            'is_trial'          => false,
            'status'            => 'active',
        ]);

        $this->artisan('subsift:check-renewal-verifications')->assertExitCode(0);

        Queue::assertNotPushed(SendRenewalVerificationJob::class);
    }
}
