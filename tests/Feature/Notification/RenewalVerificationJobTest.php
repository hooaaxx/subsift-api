<?php

namespace Tests\Feature\Notification;

use App\Actions\SendPushNotificationAction;
use App\Jobs\SendRenewalVerificationJob;
use App\Mail\RenewalVerificationMail;
use App\Models\Subscription;
use App\Models\User;
use App\Repositories\Contracts\NotificationRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RenewalVerificationJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_creates_three_tokens_sends_mail_and_creates_notification(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $sub  = Subscription::factory()->create([
            'user_id'           => $user->id,
            'next_billing_date' => Carbon::yesterday()->toDateString(),
        ]);

        (new SendRenewalVerificationJob($sub))->handle(
            app(NotificationRepositoryInterface::class),
            new SendPushNotificationAction()
        );

        $this->assertDatabaseCount('renewal_verification_tokens', 3);
        $this->assertDatabaseHas('renewal_verification_tokens', ['subscription_id' => $sub->id, 'action' => 'renewed']);
        $this->assertDatabaseHas('renewal_verification_tokens', ['subscription_id' => $sub->id, 'action' => 'cancelled']);
        $this->assertDatabaseHas('renewal_verification_tokens', ['subscription_id' => $sub->id, 'action' => 'price_changed']);

        Mail::assertSent(RenewalVerificationMail::class, fn ($m) => $m->hasTo($user->email));

        $this->assertDatabaseHas('notifications', [
            'user_id'         => $user->id,
            'subscription_id' => $sub->id,
            'channel'         => 'in_app',
            'type'            => 'renewal_verification',
        ]);
    }

    public function test_job_tokens_have_seven_day_expiry(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $sub  = Subscription::factory()->create(['user_id' => $user->id]);

        (new SendRenewalVerificationJob($sub))->handle(
            app(NotificationRepositoryInterface::class),
            new SendPushNotificationAction()
        );

        $token = $sub->renewalVerificationTokens()->first();
        $this->assertTrue($token->expires_at->isFuture());
        $this->assertTrue($token->expires_at->greaterThan(now()->addDays(6)));
    }
}
