<?php

namespace Tests\Feature\Notification;

use App\Jobs\SendRenewalAlertJob;
use App\Mail\RenewalAlertMail;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class RenewalAlertJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_creates_in_app_notification_and_sends_email(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $subscription = Subscription::factory()->create([
            'user_id'           => $user->id,
            'name'              => 'Netflix',
            'next_billing_date' => now()->addHours(60)->toDateString(),
        ]);

        (new SendRenewalAlertJob($subscription))->handle(
            app(\App\Repositories\Contracts\NotificationRepositoryInterface::class)
        );

        $this->assertDatabaseHas('notifications', [
            'user_id'         => $user->id,
            'subscription_id' => $subscription->id,
            'channel'         => 'in_app',
        ]);

        Mail::assertSent(RenewalAlertMail::class, fn ($mail) => $mail->hasTo($user->email));
    }

    public function test_check_renewals_command_dispatches_jobs(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        Subscription::factory()->create([
            'user_id'           => $user->id,
            'alert_enabled'     => true,
            'next_billing_date' => now()->addHours(60)->toDateString(),
        ]);

        $this->artisan('subsift:check-renewals')->assertExitCode(0);

        Queue::assertPushed(SendRenewalAlertJob::class, 1);
    }
}
